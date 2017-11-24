<?php
declare(strict_types = 1);

namespace Plan2net\PageForwarding\Backend;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class TcaShowitemManipulation
 *
 * @package Plan2net\PageForwarding\Backend
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class TcaShowitemManipulation implements FormDataProviderInterface {

    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result) : array
    {
        if ($result['tableName'] === 'pages') {
            $extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

            if ($extensionConfiguration['disableDomainHandling']) {
                $showItemsList = array_map(function ($item) {
                    return trim($item);
                }, explode(',',
                    $result['processedTca']['columns']['tx_pageforwarding_redirects']['config']['overrideChildTca']['types'][0]['showitem']));
                $domainKey = array_search('domain', $showItemsList);
                unset($showItemsList[$domainKey]);
                $result['processedTca']['columns']['tx_pageforwarding_redirects']['config']['overrideChildTca']['types'][0]['showitem'] = implode(',', $showItemsList);
            }
        }

        return $result;
    }

}
