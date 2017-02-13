<?php global $cnf;
	 if($cnf['analytics']['use'] == "piwik") { ?>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
	var u="<?php global $cnf; echo $cnf['analytics']['vendor']['piwik']['url'] ?>";
	_paq.push(['setTrackerUrl', u+'piwik.php']);
	_paq.push(['setSiteId', 1]);
	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="<?php global $cnf; echo $cnf['analytics']['vendor']['piwik']['image'] ?>" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
<?php } ?>
