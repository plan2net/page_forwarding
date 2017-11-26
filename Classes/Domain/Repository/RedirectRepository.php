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

}
