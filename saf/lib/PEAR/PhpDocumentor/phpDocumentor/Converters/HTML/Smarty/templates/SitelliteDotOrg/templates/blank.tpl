<div style="float: right">
<script type="text/javascript"><!--
google_ad_client = "pub-0776261361164405";
google_alternate_ad_url = "http://www.sitellite.org/adsense/120x600.html";
google_ad_width = 120;
google_ad_height = 600;
google_ad_format = "120x600_as";
google_ad_channel ="5965840350";
google_color_border = "A9B7C4";
google_color_bg = "FFFFFF";
google_color_link = "000000";
google_color_url = "EE9911";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

<h1>{$maintitle}</h1>

{if $package == 'saf'}
  <h2>Packages</h2>
  <p>
    {section name=packagelist loop=$packageindex}
      <a href="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</a><br />
    {/section}
  </p>
{/if}

{if $package != 'saf'}
  <p>Package: {$package}</p>

  {if $compiledclassindex}
    <h2>Classes</h2>
    <p>{eval var=$compiledclassindex}</p>
  {/if}

  {if $compiledfileindex}
    <h2>Files</h2>
    <p>{eval var=$compiledfileindex}</p>
  {/if}
{/if}
