<?php

// hook into Google XML Sitemaps plugin http://wordpress.org/extend/plugins/google-sitemap-generator/
add_action("sm_buildmap", array("dsSearchAgent_XmlSitemaps", "BuildSitemap"));

class dsSearchAgent_XmlSitemaps {
	static function BuildSitemap() {
		$options = get_option(DSIDXPRESS_OPTION_NAME);

		$urlBase = get_bloginfo("url");
		if (substr($urlBase, strlen($urlBase), 1) != "/") $urlBase .= "/";
		$urlBase .= dsSearchAgent_Rewrite::GetUrlSlug();

		if ( class_exists('GoogleSitemapGenerator') ) {
			$generatorObject = &GoogleSitemapGenerator::GetInstance();

			if ($generatorObject != null && isset($options["SitemapLocations"]) && is_array($options["SitemapLocations"])) {
				$location_index = 0;

				usort($options["SitemapLocations"], array("dsSearchAgent_XmlSitemaps", "CompareListObjects"));

				foreach ($options["SitemapLocations"] as $key => $value) {
					$location_sanitized = urlencode(strtolower(str_replace(array("-", " "), array("_", "-"), $value["value"])));
					$url = $urlBase . $value["type"] .'/'. $location_sanitized . '/';

					$generatorObject->AddUrl($url, time(), $options["SitemapFrequency"], floatval($value["priority"]));
				}
			}
   		}
	}

	static function CompareListObjects($a, $b)
    {
        $al = strtolower($a["value"]);
        $bl = strtolower($b["value"]);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
}

?>