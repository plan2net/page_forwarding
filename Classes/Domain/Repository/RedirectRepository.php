<?php
declare(strict_types = 1);

namespace Plan2net\PageForwarding\Domain\Repository;

use PatrickBroens\UrlForwarding\Domain\Model\Redirect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RedirectRepository
 *
 * @package Plan2net\PageForwarding\Domain\Repository
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class RedirectRepository extends \PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository
{

    /**
     * Find the redirect by path
     *
     * @param string $path The path to search for
     * @return Redirect|null
     */
    public function findByPath(string $path)
    {
        $redirect = null;

        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = static::getQueryBuilderForTable(self::TABLE_NAME);

        $result = $queryBuilder
            ->select(
                'uid',
                'sys_language_uid',
                'type',
                'forward_url',
                'internal_page',
                'parameters',
                'external_url',
                'internal_file',
                'path',
                'http_status'
            )
            ->addSelectLiteral(
                "LENGTH(REPLACE(" . self::TABLE_NAME . ".forward_url, '.*', '')) AS url_length"
            )
            ->from(self::TABLE_NAME)
            ->where(
                "TRIM(BOTH '/' FROM " . self::TABLE_NAME . ".forward_url)=" . $queryBuilder->quote(trim($path, '/'))
            )
            ->orWhere(
                self::TABLE_NAME . ".forward_url LIKE '%.*' AND LOCATE(REPLACE(TRIM(BOTH '/' FROM " . self::TABLE_NAME . ".forward_url), '.*', ''), " . $queryBuilder->quote(trim($path, '/')) . ") > 0"
            )
            ->orderBy('url_length', 'DESC')
            ->setMaxResults(1)
            ->execute()->fetch();

        if ($result) {
            $this->updateCounterAndLastHit((int)$result['uid']);

            /** @var Redirect $redirect */
            $redirect = GeneralUtility::makeInstance(
                Redirect::class,
                (int)$result['sys_language_uid'],
                (int)$result['type'],
                (string)$result['forward_url'],
                (int)$result['internal_page'],
                (string)$result['parameters'],
                (string)$result['external_url'],
                $this->getInternalFile($result),
                (string)$result['path'],
                (int)$result['http_status']
            );
        }

        return $redirect;
    }

    /**
     * Get records with the same "forward_url"
     *
     * @param string $uidEditedRecord The uid of the edited record. When new contains 'NEW'
     * @param array $editedRecord The fields of the edited record
     * @return mixed
     */
    public function getEqualRecords(string $uidEditedRecord, array $editedRecord)
    {
        $pathQuoted = $this->getDatabaseConnection()->fullQuoteStr((string)trim($editedRecord['forward_url'], '/'),
            'tx_urlforwarding_domain_model_redirect');
        $whereUid = '';

        if (strpos($uidEditedRecord, 'NEW') === false) {
            $whereUid = ' AND uid<>' . (int)$uidEditedRecord;
        }

        $extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);
        if ((bool)$extensionConfiguration['disableDomainHandling']) {
            return $this->getDatabaseConnection()->exec_SELECTgetRows(
                '
                tx_urlforwarding_domain_model_redirect.uid,
                tx_urlforwarding_domain_model_redirect.type,
                tx_urlforwarding_domain_model_redirect.parameters
            ',
                '
                tx_urlforwarding_domain_model_redirect
            ',
                '
                TRIM(BOTH \'/\' FROM tx_urlforwarding_domain_model_redirect.forward_url)=' . $pathQuoted . '
                AND tx_urlforwarding_domain_model_redirect.deleted<>1
                ' . $whereUid . '
            ',
                '
                tx_urlforwarding_domain_model_redirect.uid
            '
            );
        }
        else {
            return $this->getDatabaseConnection()->exec_SELECTgetRows(
                '
                tx_urlforwarding_domain_model_redirect.uid,
                tx_urlforwarding_domain_model_redirect.type,
                GROUP_CONCAT(tx_urlforwarding_domain_mm.uid_foreign SEPARATOR \',\') AS domainUids,
                tx_urlforwarding_domain_model_redirect.parameters
            ',
                '
                tx_urlforwarding_domain_model_redirect
                LEFT JOIN tx_urlforwarding_domain_mm
                ON tx_urlforwarding_domain_mm.uid_local = tx_urlforwarding_domain_model_redirect.uid
            ',
                '
                TRIM(BOTH \'/\' FROM tx_urlforwarding_domain_model_redirect.forward_url)=' . $pathQuoted . '
                AND tx_urlforwarding_domain_model_redirect.deleted<>1
                ' . $whereUid . '
            ',
                '
                tx_urlforwarding_domain_model_redirect.uid
            '
            );
        }
    }

}
