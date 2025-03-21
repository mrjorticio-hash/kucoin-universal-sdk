import { WebSocketMessageCallback } from '../interfaces/websocket';
import { SubInfo } from '@internal/util/sub';

// callback class, used to store callback function and related info
// callback: callback function
// id:function id
// topic:topic related
class Callback {
    constructor(
        public callback: WebSocketMessageCallback,
        public id: string,
        public topic: string,
    ) {}
}

// Callback manager
export class CallbackManager {
    private idTopicMapping: Map<string, Map<string, boolean>>;
    private topicCallbackMapping: Map<string, Callback>;
    private topicPrefix: string;

    constructor(topicPrefix: string) {
        this.idTopicMapping = new Map();
        this.topicCallbackMapping = new Map();
        this.topicPrefix = topicPrefix;
    }

    //
    isEmpty(): boolean {
        return this.idTopicMapping.size === 0 && this.topicCallbackMapping.size === 0;
    }

    // get sub info
    getSubInfo(): SubInfo[] {
        const subInfoList: SubInfo[] = [];

        for (const topics of this.idTopicMapping.values()) {
            //
            const info = new SubInfo(this.topicPrefix, [], null);

            //
            for (const topic of topics.keys()) {
                // split topic
                const parts = topic.split(':');
                // if split into 2 parts, then it's a valid topic
                if (parts.length === 2 && parts[1] !== 'all') {
                    // add the second part to info.args
                    info.args.push(parts[1]);
                }
                // check if topic exists in topicCallbackMapping
                if (this.topicCallbackMapping.has(topic)) {
                    // get the callback object
                    const callbackObj = this.topicCallbackMapping.get(topic);
                    // assign the callback
                    if (callbackObj) {
                        info.callback = callbackObj.callback;
                    }
                }
            }
            // add to list
            subInfoList.push(info);
        }

        // return list of sub info
        return subInfoList;
    }

    // add sub info
    add(subInfo: SubInfo): boolean {
        // get id
        const id = subInfo.toId();

        // if id exists, return false
        if (this.idTopicMapping.has(id)) {
            return false;
        }

        // create a new map to store topics
        const topicMap = new Map<string, boolean>();

        // iterate through all topics in subInfo
        for (const topic of subInfo.topics()) {
            // if topic exists, skip
            if (this.topicCallbackMapping.has(topic)) {
                continue;
            }

            // add topic to map
            topicMap.set(topic, true);
            // add new Callback object to topicCallbackMapping
            this.topicCallbackMapping.set(topic, new Callback(subInfo.callback!, id, topic));
        }

        // add id and topic map to idTopicMapping
        this.idTopicMapping.set(id, topicMap);
        return true;
    }

    // remove sub info
    remove(id: string): void {
        const topicMap = this.idTopicMapping.get(id);
        if (!topicMap) {
            return;
        }

        this.idTopicMapping.delete(id);
        for (const topic of topicMap.keys()) {
            this.topicCallbackMapping.delete(topic);
        }
    }

    // get callback
    get(topic: string): WebSocketMessageCallback | undefined {
        const cb = this.topicCallbackMapping.get(topic);
        return cb ? cb.callback : undefined;
    }
}

// topic manager
export class TopicManager {
    private topicPrefix: Map<string, CallbackManager>;

    constructor() {
        this.topicPrefix = new Map();
    }

    // get callback manager
    getCallbackManager(topic: string): CallbackManager {
        const parts = topic.split(':');
        let prefix = topic;
        if (parts.length === 2 && parts[1] !== 'all') {
            prefix = parts[0];
        }

        if (!this.topicPrefix.has(prefix)) {
            this.topicPrefix.set(prefix, new CallbackManager(topic));
        }
        return this.topicPrefix.get(prefix)!;
    }

    // iterate all callback managers
    range(func: (key: string, value: CallbackManager) => boolean): void {
        for (const [key, value] of this.topicPrefix.entries()) {
            if (!func(key, value)) {
                break;
            }
        }
    }
}
