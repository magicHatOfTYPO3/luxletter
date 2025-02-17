<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(
    function () {
        $languageFilePrefix = 'LLL:EXT:luxletter/Resources/Private/Language/locallang_db.xlf:';
        $frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

        /**
         * Register plugins
         */
        ExtensionUtility::registerPlugin(
            'luxletter',
            'Fe',
            $languageFilePrefix . 'flexform.fe',
            'extension-lux',
            'plugins',
            $languageFilePrefix . 'flexform.fe.description'
        );
        ExtensionUtility::registerPlugin(
            'luxletter',
            'Unsubscribe2',
            $languageFilePrefix . 'flexform.unsubscribe2',
            'extension-lux',
            'plugins',
            $languageFilePrefix . 'flexform.unsubscribe2.description'
        );

        /**
         * Disable not needed fields in tt_content
         */
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['luxletter_fe']
            = 'select_key,pages,recursive';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['luxletter_unsubscribe2']
            = 'select_key,pages,recursive';

        /**
         * Include Flexform for plugins
         */
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['luxletter_fe'] = 'pi_flexform';
        ExtensionManagementUtility::addPiFlexFormValue(
            'luxletter_fe',
            'FILE:EXT:luxletter/Configuration/FlexForm/FlexFormFe.xml'
        );
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['luxletter_unsubscribe2'] = 'pi_flexform';
        ExtensionManagementUtility::addPiFlexFormValue(
            'luxletter_unsubscribe2',
            'FILE:EXT:luxletter/Configuration/FlexForm/FlexFormUnsubscribe2.xml'
        );

        /**
         * Register new CType Teaser
         */
        ExtensionManagementUtility::addTcaSelectItem(
            'tt_content',
            'CType',
            [
                $languageFilePrefix . 'ctype.teaser',
                'teaser',
                'teaser',
            ]
        );

        /**
         * Manipulate tt_content TCA
         */
        $tca = [
            'ctrl' => [
                'typeicons' => [
                    'teaser' => 'teaser',
                ],
                'typeicon_classes' => [
                    'teaser' => 'teaser',
                ],
            ],
            'types' => [
                'teaser' => [
                    'showitem' => '
                        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        --palette--;;general,
                        header;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header.ALT.html_formlabel,
                        pi_flexform,
                        --div--;' . $frontendLanguageFilePrefix . 'tabs.appearance,
                        --palette--;' . $frontendLanguageFilePrefix . 'palette.appearanceLinks;appearanceLinks,
                        --div--;' . $frontendLanguageFilePrefix . 'tabs.access,
                        --palette--;' . $frontendLanguageFilePrefix . 'palette.hidden;hidden,
                        --palette--;' . $frontendLanguageFilePrefix . 'palette.visibility;visibility,
                        --palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,',
                ],
            ],
        ];
        $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $tca);

        /**
         * Include Flexform for teaser content element
         */
        ExtensionManagementUtility::addPiFlexFormValue(
            '*',
            'FILE:EXT:luxletter/Configuration/FlexForm/FlexFormCeTeaser.xml',
            'teaser'
        );
    }
);
