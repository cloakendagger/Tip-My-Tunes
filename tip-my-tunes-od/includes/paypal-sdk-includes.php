<?php
// Ensure the PayPal SDK files are included
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Common/PayPalModel.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Rest/IResource.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Common/PayPalResourceModel.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Rest/ApiContext.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Auth/OAuthTokenCredential.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/CartBase.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/TransactionBase.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/Payer.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/Amount.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/Transaction.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/RedirectUrls.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/Payment.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Security/Cipher.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Validation/NumericValidator.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Converter/FormatConverter.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Validation/UrlValidator.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Transport/PayPalRestCall.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Core/PayPalLoggingManager.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Core/PayPalHttpConfig.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Core/PayPalHttpConnection.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Core/PayPalConstants.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Core/PayPalConfigManager.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Log/PayPalLogFactory.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Log/PayPalDefaultLogFactory.php';
require_once plugin_dir_path(__FILE__) . '../paypal/Psr/Log/LoggerInterface.php';
require_once plugin_dir_path(__FILE__) . '../paypal/Psr/Log/AbstractLogger.php';
require_once plugin_dir_path(__FILE__) . '../paypal/Psr/Log/LogLevel.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Log/PayPalLogger.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Handler/IPayPalHandler.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Handler/RestHandler.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Common/PayPalUserAgent.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Cache/AuthorizationCache.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Handler/OauthHandler.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Common/ReflectionUtil.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Common/ArrayUtil.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Api/Links.php';

// Include PayPal exceptions
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalConnectionException.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalConfigurationException.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalMissingCredentialException.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalInvalidCredentialException.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalConfigurationException.php';
require_once plugin_dir_path(__FILE__) . '../paypal/PayPal/Exception/PayPalInvalidCredentialException.php';
?>
