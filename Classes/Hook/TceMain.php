<?php
declare(strict_types=1);

namespace Plan2net\PageForwarding\Hook;

use PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TceMain
 *
 * @package Plan2net\PageForwarding\Hook
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class TceMain extends \PatrickBroens\UrlForwarding\Hook\TceMain {

    /**
     * We have to overwrite the parent method
     * as using no domain leads to an exception with trim below
     *
     *  !!! Can be removed after https://github.com/patrickbroens/TYPO3.UrlForwarding/pull/17
     * is merged !!!
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $referringObject
     * @return void
     */
    public function processDatamap_beforeStart(DataHandler $referringObject)
    {
        if (!isset($referringObject->datamap['tx_urlforwarding_domain_model_redirect'])) {
            return;
        }

        $allowed = true;

        $redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class);

        foreach ($referringObject->datamap['tx_urlforwarding_domain_model_redirect'] as $uidEditedRecord => $editedRecord) {

            $equalRecords = $redirectRepository->getEqualRecords((string)$uidEditedRecord, $editedRecord);

            // Does the url exist in another record
            if (!empty($equalRecords)) {

                // The edited record does not have domains assigned, so not allowed
                if (empty($editedRecord['domain'])) {
                    $allowed = false;

                    // Lets test on domains
                } else {
                    $editedRecord['domain'] = trim($editedRecord['domain'], ',');

                    foreach ($equalRecords as $equalRecord) {
                        if ($equalRecord['domainUids'] === null) {
                            $allowed = false;
                            break;
                        } else {
                            $editedRecordDomainUids = GeneralUtility::intExplode(',', $editedRecord['domain']);
                            $equalRecordDomainUids = GeneralUtility::intExplode(',', $equalRecord['domainUids']);

                            $equalDomainUids = array_intersect($editedRecordDomainUids, $equalRecordDomainUids);

                            if (!empty($equalDomainUids)) {
                                $allowed = false;
                                break;
                            }
                        }
                    }
                }
            }

            if (!$allowed) {
                unset($referringObject->datamap['tx_urlforwarding_domain_model_redirect'][$uidEditedRecord]);

                /** @var FlashMessage $flashMessage */
                $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    'A redirect with the name "'
                    . htmlspecialchars($editedRecord['forward_url'])
                    . '" is already covering the same domain. This record has not been stored.',
                    'An error occured',
                    FlashMessage::ERROR,
                    true
                );
                $this->getFlashMessageQueue()->addMessage($flashMessage);
            }
        }
    }

    /**
     * @param array $incomingFieldArray
     * @param string $table
     * @param integer $id
     * @param DataHandler $parent
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, DataHandler $parent) {
        if ($table != 'tx_urlforwarding_domain_model_redirect') {
            return;
        }

        // if it is a new record
        if (!is_numeric($id)) {
            $extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

            $incomingFieldArray['http_status'] = $incomingFieldArray['http_status'] ?? $extensionConfiguration['defaultHttpStatusCode'];
        }
    }

}
