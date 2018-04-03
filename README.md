# Google Tag Manager (GTM) plugin for Wordpress

## Installation
Upload *fk-google-tag-manager* to the /wp-content/plugins/ directory

Activate the plugin through the 'Plugins' menu in WordPress

## Usage

Add a hook **`do_action('wp_body')`** after opening tag **&lt;body&gt;**

```php
<body>
<?php echo do_action('wp_body'); ?>
... other code
```

Go to Settings > General and set the ID from your Google Tag Manager account.

Use comma without space (,) to enter multiple IDs.
