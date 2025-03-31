from __future__ import annotations

import json
from typing import Optional, Any, Dict

from pydantic import BaseModel, ConfigDict, Field

from .constants import RestResultCode


class RestRateLimit(BaseModel):
    """
    RestRateLimit represents the rate limiting information for a REST API.

    Attributes:
        limit (int): Total resource pool quota
        remaining (int): Remaining resource pool quota
        reset (int): Resource pool quota reset countdown (in milliseconds)
    """

    limit: Optional[int] = Field(default=None)
    remaining: Optional[int] = Field(default=None)
    reset: Optional[int] = Field(default=None)

    model_config = ConfigDict(
        populate_by_name=True,
        validate_assignment=False,
        protected_namespaces=(),
    )


class RestResponse(BaseModel):
    """
    RestResponse represents a generic response from the REST API.

    Attributes:
        code (str): Response code
        data (any): Response data
        message (str): Error message
        rate_limit (RestRateLimit): RestRateLimit info
    """

    code: Optional[str] = Field(default=None)
    data: Any = Field(default=None)
    message: Optional[str] = Field(default=None, alias="msg")
    rate_limit: Optional[RestRateLimit] = Field(default=None)

    model_config = ConfigDict(
        populate_by_name=True,
        validate_assignment=False,
        protected_namespaces=(),
    )

    def error(self):
        """
        Check if there is an error in the response.
        """
        if self.code == RestResultCode.SUCCESS.value:
            return None
        return Exception(f"Server returned an error, code: {self.code}, msg: {self.message}")

    @classmethod
    def from_json(cls, json_str: str) -> Optional[RestResponse]:
        return cls.from_dict(json.loads(json_str))

    def to_dict(self) -> Dict[str, Any]:
        _dict = self.model_dump(
            by_alias=True,
            exclude_none=True,
        )
        return _dict

    @classmethod
    def from_dict(
            cls, obj: Optional[Dict[str, Any]]) -> Optional[RestResponse]:
        if obj is None:
            return None

        if not isinstance(obj, dict):
            return cls.model_validate(obj)

        _obj = cls.model_validate({
            "code": obj.get("code"),
            "data": obj.get("data"),
            "message": obj.get("msg")
        })
        return _obj


class RestError(RuntimeError):

    def __init__(self, response: RestResponse, msg):
        self.response = response
        self.msg = msg

    def __str__(self):
        if self.response is not None:
            return f"request error, server code: {self.response.code}, server msg: {self.response.message}, context msg: {self.msg}"
        return f"request error, context msg: {self.msg}"

    def get_common_response(self) -> Optional[RestResponse]:
        return self.response


class WsMessage(BaseModel):
    """
    WsMessage represents a message between the WebSocket client and server.

    Attributes:
        id (Optional[str]): Unique message ID.
        type (Optional[str]): Message type (e.g., "ping", "subscribe", etc.).
        sn (Optional[int]): Sequence number of the message.
        topic (Optional[str]): The topic to which the message pertains.
        subject (Optional[str]): Subject of the message.
        private_channel (Optional[bool]): Indicates if the message is for a private channel.
        response (Optional[bool]): Indicates if the message is a response from the server.
        raw_data (Any): Raw message data in its original format.
    """
    id: Optional[str] = Field(default=None, description="Unique message ID.")
    type: Optional[str] = Field(default=None, alias="type",
                                description="Message type (e.g., \"ping\", \"subscribe\", etc.).")
    sn: Optional[int] = Field(default=None, description="Sequence number.")
    topic: Optional[str] = Field(default=None, description="The topic of the message.")
    subject: Optional[str] = Field(default=None, description="Subject of the message.")
    private_channel: Optional[bool] = Field(default=None, alias="privateChannel",
                                            description="Indicates if it is a private channel.")
    response: Optional[bool] = Field(default=None, description="Indicates if the message is a response.")
    raw_data: Any = Field(default=None, alias="data", description="Raw message data")

    model_config = {
        "populate_by_name": True
    }

    def to_json(self) -> str:
        return self.model_dump_json(by_alias=True, exclude_none=True)

    @classmethod
    def from_json(cls, json_str: str) -> Optional["WsMessage"]:
        data = json.loads(json_str)
        return cls.model_validate(data)

    @classmethod
    def from_dict(cls, obj: Optional[Dict[str, Any]]) -> Optional["WsMessage"]:
        if obj is None:
            return None
        return cls.model_validate(obj)
