<?php

    define("BASE_URL", "http://nova.it.rit.edu/~pjm8632/539/project3/A/service/");
    define("NEWS_URL", BASE_URL . "get_news.php");
    define("ADS_URL", BASE_URL . "get_ads.php");
    define("BANNER_URL", BASE_URL . "get_banner.php");
    define("EDITORIAL_URL", BASE_URL . "get_editorial.php");
    define("EDITIONS_URL", BASE_URL . "get_editions.php");
    define("SUBMIT_AD", BASE_URL . "submit_ad.php");

    require_once ("MyCurl.class.php");

    /**
     * Submits the ad using the MyCurl class and returns the reponse from the
     * server
     * Expects the array passed in is associative and simulates a $_POST array
     */
    function submitAd($post) {
        if (fileExists(SUBMIT_AD)) {
            return MyCurl::sendPost(SUBMIT_AD, $post);
        } else {
            return 'Something went wrong on the server, could not submit your ad.';
        }
    }

    /**
     * Goes off and gets the editions from the backend and returns them as an
     * assoicative array.
     * The keys will be the id for the edition, and the value is the name
     */
    function getEditions() {
        $result = null;

        if (fileExists(EDITIONS_URL)) {
            // get the xml
            $dom = new DOMDocument();
            $dom -> load(EDITIONS_URL);

            // parse the dom
            // <edition>
            //	 <id>1</id>
            //	 <name>Sports</name>
            // </edition>

            $editions = $dom -> getElementsByTagName("edition");

            // check to see if we have anything
            if ($editions -> length > 0) {
                // start up the array
                $result = array();

                // cycle thru the addition
                foreach ($editions as $edition) {
                    // get edition data
                    $id = $edition -> getElementsByTagName("id") -> item(0) -> nodeValue;
                    $name = $edition -> getElementsByTagName("name") -> item(0) -> nodeValue;

                    // add to data
                    $result[$id] = $name;
                }
            }
        }

        return $result;
    }

    /**
     * Checks to see if the url exists by using cURL to check if we get a 404
     */
    function fileExists($url) {
        $result = true;

        // add error checking if url exits
        if (MyCurl::getStatusCode($url) == 404) {
            $result = false; ;
        }

        return $result;
    }

    /**
     * Accepts what should be a 1 item node list.
     * Determines if the length is at least greater than 0, then grabs the first
     * item's value.
     * If its length is 0 or less, returns an empty string
     */
    function getNodeValue($domNode) {
        if ($domNode -> length > 0) {
            $domNode = $domNode -> item(0) -> nodeValue;
        } else {
            $domNode = "";
        }

        return $domNode;
    }

    function getBannerFromBackend() {
        return MyCurl::getRemoteFile(BANNER_URL);
    }

    function getEditorialFromBackend() {
        return MyCurl::getRemoteFile(EDITORIAL_URL);
    }

    /**
     * Gets the news items specified by the page number and the number of news
     * pages.
     * Uses the getItems() to get an associative array
     *
     */
    function getNewsInfo($pageNum, $count) {
        return getItem(NEWS_URL . "?page=$pageNum&count=$count");
    }

    /**
     * Gets the ads items specified by the page number and the number of news
     * pages.
     * Uses the getItems() to get an associative array
     *
     */
    function getAdsInfo($pageNum, $count) {
        return getItem(ADS_URL . "?page=$pageNum&count=$count");
    }

    /**
     * Assumes that the url contains an xml document with the following
     * structure:
     * <page <page pageNumber="1" totalPages="7" numberPerPage="5">
     <item>
     <id>208</id>
     <subject>Intel profits beat expectations</subject>
     <content>
     <![CDATA[
     The world's largest maker of computer chips, Intel, saw quarterly profits
     beat Wall Street forecasts, amid promises to ramp up spending.
     ]]>
     </content>
     <pubdate>2012-01-19 23:55:03</pubdate>
     <editions>
     <edition>Sports</edition>
     <edition>Technology</edition>
     <edition>Funnies</edition>
     </editions>
     </item>
     *
     *
     *This function parses the xml document and returns an associative array
     * filled with data
     */
    function getItem($url) {
        // TODO: add error checking if url exits
        if (!fileExists($url)) {
            return null;
        }

        // create a DOM object to parse the page
        $dom = new DOMDocument();
        $dom -> load($url);

        // grab the page info
        $pageNumber = $dom -> getElementsByTagName('page') -> item(0) -> getAttribute("pageNumber");
        $totalPages = $dom -> getElementsByTagName('page') -> item(0) -> getAttribute("totalPages");
        $numberPerPage = $dom -> getElementsByTagName('page') -> item(0) -> getAttribute("numberPerPage");

        // grab the item info
        $data = array();

        $items = $dom -> getElementsByTagName('item');

        foreach ($items as $key => $item) {
            // grab the id
            $id = getNodeValue($item -> getElementsByTagName('id'));

            // grab the subject
            $subject = getNodeValue($item -> getElementsByTagName('subject'));

            // if we don't have a subject, then we must have a title
            if (!$subject) {
                $subject = getNodeValue($item -> getElementsByTagName('title'));
            }

            // grab the content
            $content = getNodeValue($item -> getElementsByTagName('content'));

            // grab the pub data
            $pubDate = getNodeValue($item -> getElementsByTagName('pubdate'));

            // grab all the editions
            $editions = array();

            $editionNodes = $dom -> getElementsByTagName('edition');
            foreach ($editionNodes as $oneEdition) {
                $editions[] = $oneEdition -> nodeValue;
            }

            // add as array to each item
            $data[] = array("id" => $id, "subject" => $subject, "content" => $content, "pubDate" => $pubDate, "editions" => $editions);
        }

        // build up the results
        $result = array();
        $result['pageNumber'] = $pageNumber;
        $result['totalPages'] = $totalPages;
        $result['numberPerPage'] = $numberPerPage;
        // add the items
        $result['items'] = $data;

        return $result;
    }
?>