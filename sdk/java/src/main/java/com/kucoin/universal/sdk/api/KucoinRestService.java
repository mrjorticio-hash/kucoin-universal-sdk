package com.kucoin.universal.sdk.api;

import com.kucoin.universal.sdk.generate.service.AccountService;
import com.kucoin.universal.sdk.generate.service.AffiliateService;
import com.kucoin.universal.sdk.generate.service.BrokerService;
import com.kucoin.universal.sdk.generate.service.CopyTradingService;
import com.kucoin.universal.sdk.generate.service.EarnService;
import com.kucoin.universal.sdk.generate.service.FuturesService;
import com.kucoin.universal.sdk.generate.service.MarginService;
import com.kucoin.universal.sdk.generate.service.SpotService;
import com.kucoin.universal.sdk.generate.service.VIPLendingService;

/**
 * Interface KucoinRestService Defines the contract for accessing KuCoin REST API service groups.
 */
public interface KucoinRestService {

  /**
   * Provides functions to access and manipulate account-related data.
   *
   * @return AccountService
   */
  AccountService getAccountService();

  /**
   * Provides functions to access affiliate-related data.
   *
   * @return AffiliateService
   */
  AffiliateService getAffiliateService();

  /**
   * Provides functions to access and manage broker-related data.
   *
   * @return BrokerService
   */
  BrokerService getBrokerService();

  /**
   * Provides functions to access and manage copy trading-related data.
   *
   * @return CopyTradingService
   */
  CopyTradingService getCopytradingService();

  /**
   * Provides functions to access and manage earn-related data.
   *
   * @return EarnService
   */
  EarnService getEarnService();

  /**
   * Provides functions to perform actions in the futures market.
   *
   * @return FuturesService
   */
  FuturesService getFuturesService();

  /**
   * Provides functions to access and manage margin-related data.
   *
   * @return MarginService
   */
  MarginService getMarginService();

  /**
   * Provides functions to perform actions in the spot market.
   *
   * @return SpotService
   */
  SpotService getSpotService();

  /**
   * Provides functions to access and manage VIP lending-related data.
   *
   * @return VIPLendingService
   */
  VIPLendingService getVipLendingService();
}
