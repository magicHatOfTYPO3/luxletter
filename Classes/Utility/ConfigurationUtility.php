<?php

declare(strict_types=1);
namespace In2code\Luxletter\Utility;

use In2code\Luxletter\Exception\MisconfigurationException;
use LogicException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class ConfigurationUtility
{
    const PAGE_TYPE_MULTILANGUAGEMODE_DEFAULT = 11;

    /**
     * Get TypoScript settings
     *
     * @return array
     * @throws InvalidConfigurationTypeException
     */
    public static function getExtensionSettings(): array
    {
        return ObjectUtility::getConfigurationManager()->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'luxletter',
        );
    }

    /**
     * @return string like "https://www.domain.org/"
     */
    public static function getCurrentDomain(): string
    {
        if (GeneralUtility::getIndpEnv('HTTP_HOST') === null) {
            throw new LogicException(__FUNCTION__ . ' must not be called from CLI context', 1622812071);
        }
        $uri = parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'), PHP_URL_SCHEME);
        $uri .= '://' . GeneralUtility::getIndpEnv('HTTP_HOST') . '/';
        return $uri;
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isMultiLanguageModeActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(
            'luxletter',
            'multiLanguageMode'
        ) === '1';
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isReceiverActionActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('luxletter', 'receiverAction') === '1';
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isUnsubscribeUrlToMailHeaderActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('luxletter', 'addUnsubscribeUrlToMailHeader') !== '0';
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isRewriteLinksInNewsletterActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(
            'luxletter',
            'rewriteLinksInNewsletter'
        ) === '1';
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isImageEmbeddingActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(
            'luxletter',
            'embedImagesInNewsletter'
        ) === '1';
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isAsynchronousQueueStorageActivated(): bool
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(
            'luxletter',
            'asynchronousQueueStorage'
        ) === '1';
    }

    /**
     * @return int
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getMultilanguageNewsletterPageDoktype(): int
    {
        $pageType = (int)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(
            'luxletter',
            'multiLanguageNewsletterPageDoktype'
        );
        if ($pageType === 0) {
            $pageType = self::PAGE_TYPE_MULTILANGUAGEMODE_DEFAULT;
        }
        return $pageType;
    }

    /**
     * @return int
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function getTypeNumToNumberLocation(): int
    {
        return (int)GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('luxletter', 'addTypeNumToNumberLocation');
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public static function isContextFitting(): bool
    {
        if (self::isLimitToContextActivated() === false) {
            return true;
        }
        $allowedContext = self::getLimitToContext();
        $currentApplicationContext = Environment::getContext()->__toString();
        return stristr($currentApplicationContext, $allowedContext) !== false;
    }

    /**
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected static function isLimitToContextActivated(): bool
    {
        return self::getLimitToContext() !== '';
    }

    /**
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    protected static function getLimitToContext(): string
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('luxletter', 'limitToContext');
    }

    /**
     * @return string
     * @throws MisconfigurationException
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getEncryptionKey(): string
    {
        $configurationManager = ObjectUtility::getConfigurationManagerCore();
        $encryptionKey = $configurationManager->getLocalConfigurationValueByPath('SYS/encryptionKey');
        if (empty($encryptionKey)) {
            throw new MisconfigurationException('No encryption key found in this TYPO3 installation', 1562069158);
        }
        return $encryptionKey;
    }

    public static function isTypo3Version12(): bool
    {
        return (new Typo3Version())->getMajorVersion() === 12;
    }

    public static function isCli(): bool
    {
        return Environment::isCli();
    }
}
