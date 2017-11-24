<?php
declare(strict_types = 1);

namespace Plan2net\PageForwarding\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ForwardController
 *
 * @package Plan2net\PageForwarding\Controller
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class ForwardController extends \PatrickBroens\UrlForwarding\Controller\ForwardController {

    /**
     * Check if a redirect exists and forward according to the redirect url and status
     */
    public function forwardIfExists()
    {
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');

        $request = parse_url($url);

        $path = trim($request['path'], '/');
        $host = $request['host'];
        $scheme = $request['scheme'];

        if ($path !== '') {
            $extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

            if ($extensionConfiguration['disableDomainHandling']) {
                $redirect = $this->redirectRepository->findByPath($path);
            }
            else {
                $redirect = $this->redirectRepository->findByPathAndDomain($host, $path);
            }

            if ($redirect) {
                $this->redirect($redirect, $scheme, $host, $path);
            }
        }
    }

}
