# page_forwarding
Manage URL redirects in pages

This extension is based on https://github.com/patrickbroens/TYPO3.UrlForwarding

## Extension Manager configuration

- `disableDomainHandling` This disables any multi-domain handling (set to 1 if you have only one target domain for redirects)
- `storagePid` This is the ID of a system folder, where all the redirect records will be stored (required)

## Add redirects in page settings

You can add new redirects at the bottom of the `General` tab in the page settings. Redirects are only possible in the main page record (not in translations of pages), but you can just add another redirect and select a different target language.

## Wildcards

You can add a `.*` to the end of the forward URL to make any URL match that starts with the given string. This is useful if you want to redirect a whole part of your old website to a new entry page.
