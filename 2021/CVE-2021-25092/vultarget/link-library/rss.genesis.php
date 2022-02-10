<?php

  /* ================================================== \\

  You have received a copy of the GNU Lesser General PublicLicense along with this library.
  Read it to know more about "copyright", modification and redistribution terms.
  
  RSS Genesis 1.1
  :...> About: Generic class to generate RSS valid feeds.
  :...> Author: Yannick Lefebvre
  :...> Based on the work of: Klaus Roberto de Paiva klausphp@gmail.com
  :...> Contact: ylefebvre@gmail.com
  :...> Date: 02/24/2006
  :...> Latest Version: http://rssgenesis.sourceforge.net/
  :...> License: LGPL (GNU Lesser General PublicLicense)
    
  \\ ================================================== */
  
  // This is the rssGenesis class. PHP4 syntax. PHP4 and PHP5 compatible. //
  class rssGenesis {
    
    // Class Variables Declaration //
    
    // Static header to RSS 0.91 feeds //
    var $rss_header = "<?xml version=\"1.0\"?>\r\n<rss version=\"2.0\">\r\n";
    
    // Template to channel sections //
    var $rss_channel = "\t<channel>\r\n\t\t<title>{channel_Title}</title>\r\n\t\t<link>{channel_Link}</link>\r\n\t\t<description>{channel_Description}</description>\r\n\t\t<language>{channel_Language}</language>\r\n\t\t<copyright>{channel_Copyright}</copyright>\r\n\t\t<managingEditor>{channel_ManagingEditor}</managingEditor>\r\n\t\t<webMaster>{channel_WebMaster}</webMaster>\r\n\t\t<rating>{channel_Rating}</rating>\r\n\t\t<pubDate>{channel_PubDate}</pubDate>\r\n\t\t<lastBuildDate>{channel_LastBuildDate}</lastBuildDate>\r\n\t\t<category>{channel_Category}</category>\r\n\t\t<generator>RSS Genesis 1.1</generator>\r\n\t\t<docs>{channel_Docs}</docs>\r\n\t\t<skipDays>{channel_SkipDays}</skipDays>\r\n\t\t<skipHours>{channel_SkipHours}</skipHours>\r\n\r\n";
    
    // Template to image sections //
    var $rss_image = "\t\t<image>\r\n\t\t\t<title>{image_Title}</title>\r\n\t\t\t<url>{image_Source}</url>\r\n\t\t\t<link>{image_Link}</link>\r\n\t\t\t<width>{image_Width}</width>\r\n\t\t\t<height>{image_Height}</height>\r\n\t\t\t<description>{image_Description}</description>\r\n\t\t</image>\r\n\r\n";
    
    // Template to item sections //
    var $rss_item = "\t\t<item>\r\n\t\t\t<title>{item_Title}</title>\r\n\t\t\t<link>{item_Link}</link>\r\n\t\t\t<description>{item_Description}</description>\r\n\t\t\t<pubDate>{item_PubDate}</pubDate>\r\n\t\t\t<category>{item_category}</category>\r\n\t\t</item>\r\n\r\n";
    
    // Template to input sections //
    var $rss_input = "\t\t<textinput>\r\n\t\t\t<title>{input_Title}</title>\r\n\t\t\t<description>{input_Description}</description>\r\n\t\t\t<name>{input_Name}</name>\r\n\t\t\t<link>{input_Link}</link>\r\n\t\t\t</textinput>\r\n\r\n";
    
    // Static footer to RSS 0.91 feeds //
    var $rss_footer = "\t</channel>\r\n</rss>";
    
    // Holds all RSS contents //
    var $rss_feed = null;
    
    // Holds channel data //
    var $channel_data = null;
    
    // Holds image data //
    var $image_data = null;
    
    // Holds all item data //
    var $item_data = Array();
    
    // Holds input data //
    var $input_data = null;
		
	// Holds RSS Version
	var $rssformat = "RSS20";
	
	// Constructor function for rssGenesis Class
	// Valid values for the rssversion parameter are: RSS20, RSS091
	function __construct ($rssversion = "RSS20") {
		$this->rssformat = $rssversion;
		if ($rssversion == "RSS091")
		{
			$this->rss_header = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\r\n\r\n<!DOCTYPE rss PUBLIC \"-//Netscape Communications//DTD RSS 0.91//EN\" \"http://my.netscape.com/publish/formats/rss-0.91.dtd\">\r\n\r\n<rss version=\"0.91\">\r\n";
			
			$this->rss_channel = "\t<channel>\r\n\t\t<title>{channel_Title}</title>\r\n\t\t<link>{channel_Link}</link>\r\n\t\t<description>{channel_Description}</description>\r\n  <language>{channel_Language}</language>\r\n\t\t<copyright>{channel_Copyright}</copyright>\r\n\t\t<managingEditor>{channel_ManagingEditor}</managingEditor>\r\n\t\t<webMaster>{channel_WebMaster}</webMaster>\r\n\t\t<rating>{channel_Rating}</rating>\r\n\t\t<pubDate>{channel_PubDate}</pubDate>\r\n\t\t<lastBuildDate>{channel_LastBuildDate}</lastBuildDate>\r\n\t\t<docs>{channel_Docs}</docs>\r\n\t\t<skipDays>{channel_SkipDays}</skipDays>\r\n\t\t<skipHours>{channel_SkipHours}</skipHours>\r\n\r\n";
			
			$this->rss_image = "\t\t<image>\r\n\t\t\t<title>{image_Title}</title>\r\n\t\t\t<url>{image_Source}</url>\r\n\t\t\t<link>{image_Link}</link>\r\n\t\t\t<width>{image_Width}</width>\r\n\t\t\t<height>{image_Height}</height>\r\n\t\t\t<description>{image_Description}</description>\r\n\t\t\t</image>\r\n\r\n";
			
			$this->rss_item = "\t\t<item>\r\n\t\t\t<title>{item_Title}</title>\r\n\t\t\t<link>{item_Link}</link>\r\n\t\t\t<description>{item_Description}</description>\r\n\t\t\t</item>\r\n\r\n";
		}
	}
    
    // Creates channel data handler //
    function setChannel ($title = "", $link = "", $description = "", $language = "", $copyright = "", $managingEditor = "", $webMaster = "", $rating = "", $pubDate = "", $lastBuildDate = "", $category = "", $docs = "", $timetolive = "", $skipDays = "", $skipHours = "") {
      
      // Copies the original template to channel sections //
      $this->channel_data = $this->rss_channel;
      
      // Null values become empty values // Start
      $title = ($title == null) ? "" : $title;
      $link = ($link == null) ? "" : $link;
      $description = ($description == null) ? "" : $description;
      $language = ($language == null) ? "" : $language;
      $copyright = ($copyright == null) ? "" : $copyright;
      $managingEditor = ($managingEditor == null) ? "" : $managingEditor;
      $webMaster = ($webMaster == null) ? "" : $webMaster;
      $rating = ($rating == null) ? "" : $rating;
      $pubDate = ($pubDate == null) ? "" : $pubDate;
      $lastBuildDate = ($lastBuildDate == null) ? "" : $lastBuildDate;
	  $category = ($category == null) ? "" : $category;
	  $generator = ( !isset( $generator ) || $generator == null) ? "" : $generator;
      $docs = ($docs == null) ? "" : $docs;
	  $timetolive = ($timetolive == null) ? "" : $timetolive;
      $skipDays = ($skipDays == null) ? "" : $skipDays;
      $skipHours = ($skipHours == null) ? "" : $skipHours;
      // Null values become empty values // End
      
      // Title parser // Convertes quotes and strips backslashes!
      $title = stripslashes (htmlspecialchars (trim ($title), ENT_QUOTES));
      
      // Title parser // Empty titles not allowed
      $title = (empty ($title)) ? "RSS Feed - RSS Genesis 1.1" : $title;
      
      // Link parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp(s)?://)", $link)) :
      
        $link = "http://rssgenesis.sourceforge.net/";
      
      endif;
      
      // Description parser // Convertes quotes and strips backslashes!
      $description = stripslashes (htmlspecialchars (trim ($description), ENT_QUOTES));
      
      // Description parser // Empty descriptions not allowed
      $description = (empty ($description)) ? "A basic and simple RSS Feed!" : $description;
      
      // Language parser // Only [-A-Za-z]
      if ((preg_match ("([^-A-Za-z])", $language)) or (empty ($language))) :
      
        $language = "en-us";
      
      endif;
      
      // Copyright parser // Optional data
      if (empty ($copyright)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<copyright>{channel_Copyright}</copyright>", "", $this->channel_data);
        
      endif;
      
      // Managing Editor parser // Optional data
      if (empty ($managingEditor)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<managingEditor>{channel_ManagingEditor}</managingEditor>", "", $this->channel_data);
      
      endif;
      
      // WebMaster parser // Optional data
      if (empty ($webMaster)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<webMaster>{channel_WebMaster}</webMaster>", "", $this->channel_data);
      
      endif;
      
      // Rating parser // Optional data
      if (empty ($rating)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<rating>{channel_Rating}</rating>", "", $this->channel_data);
      
      endif;
      
      // PubDate parser // If is set to auto, autogenerates it
      if ($pubDate == "auto") :
      
        $pubDate = date ("r");
      
      endif;
      
      // PubDate parser // Optional data
      if (empty ($pubDate)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<pubDate>{channel_PubDate}</pubDate>", "", $this->channel_data);
      
      endif;
      
      // Last Build Date parser // If is set to auto, autogenerates it
      if ($lastBuildDate == "auto") :
      
        $lastBuildDate = date ("r");
      
      endif;
      
      // Last Build Date parser // Optional data
      if (empty ($lastBuildDate)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<lastBuildDate>{channel_LastBuildDate}</lastBuildDate>", "", $this->channel_data);
      
      endif;
	  
	  if ($this->rssformat == "RSS20") {
		  // Category parser // Optional data
		  if (empty ($category)) :
		  
			$this->channel_data = str_replace ("\r\n\t\t<category>{channel_Category}</category>", "", $this->channel_data);
		  
		  endif;
		}
      
      // Docs parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp://)", $docs)) :
      
        $docs = "http://rssgenesis.sourceforge.net/links/";
      
      endif;
      
      // Skip Days parser // Generate data
      if (!empty ($skipDays)) :
      
        // Starts the complete Skip Days storage variable //
        $skipDaysComplete = "\r\n";
        
        // Explodes the string to get all skipped days //
        $skipDays = explode ("|", $skipDays);
        
        // For each element given //
        foreach ($skipDays as $days) :
        
          // Inserts data to skipped day //
          $skipDaysComplete .= "    <day>$days</day>\r\n";
          
        endforeach;
        
        // Overwrites the given Skip Days variable //
        $skipDays = $skipDaysComplete .= "  ";
        
      endif;
      
      // Skip Days parser // Optional data
      if (empty ($skipDays)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<skipDays>{channel_SkipDays}</skipDays>", "", $this->channel_data);
      
      endif;
      
      // Skip Hours parser // Generate data
      if (!empty ($skipHours)) :
      
        // Starts the complete Skip Hours storage variable //
        $skipHoursComplete = "\r\n";
        
        // Explodes the string to get all skipped hours //
        $skipHours = explode ("|", $skipHours);
        
        // For each element given //
        foreach ($skipHours as $hours) :
        
          // Inserts data to skipped hour //
          $skipHoursComplete .= "    <hour>$hours</hour>\r\n";
          
        endforeach;
        
        // Overwrites the given Skip Hours variable //
        $skipHours = $skipHoursComplete .= "  ";
        
      endif;
      
      // Skip Hours parser // Optional data
      if (empty ($skipHours)) :
      
        $this->channel_data = str_replace ("\r\n\t\t<skipHours>{channel_SkipHours}</skipHours>", "", $this->channel_data);
      
      endif;
      
      // Inserts channel title // Replaces {channel_Title}
      $this->channel_data = str_replace ("{channel_Title}", $title, $this->channel_data);
      
      // Inserts channel link // Replaces {channel_Link}
      $this->channel_data = str_replace ("{channel_Link}", $link, $this->channel_data);
      
      // Inserts channel language // Replaces {channel_Language}
      $this->channel_data = str_replace ("{channel_Language}", $language, $this->channel_data);
      
      // Inserts channel description // Replaces {channel_Description}
      $this->channel_data = str_replace ("{channel_Description}", $description, $this->channel_data);
      
      // Inserts channel copyright // Replaces {channel_Copyright}
      $this->channel_data = str_replace ("{channel_Copyright}", $copyright, $this->channel_data);
      
      // Inserts channel managingEditor // Replaces {channel_ManagingEditor}
      $this->channel_data = str_replace ("{channel_ManagingEditor}", $managingEditor, $this->channel_data);
      
      // Inserts channel webMaster // Replaces {channel_WebMaster}
      $this->channel_data = str_replace ("{channel_WebMaster}", $webMaster, $this->channel_data);
      
      // Inserts channel rating // Replaces {channel_Rating}
      $this->channel_data = str_replace ("{channel_Rating}", $rating, $this->channel_data);
      
      // Inserts channel pubDate // Replaces {channel_PubDate}
      $this->channel_data = str_replace ("{channel_PubDate}", $pubDate, $this->channel_data);
      
      // Inserts channel lastBuildDate // Replaces {channel_LastBuildDate}
      $this->channel_data = str_replace ("{channel_LastBuildDate}", $lastBuildDate, $this->channel_data);
	  
	  if ($this->rssformat == "RSS20") {
		// Inserts channel category // Replaces {channel_Category}
		$this->channel_data = str_replace ("{channel_Category}", $category, $this->channel_data);
	  }
      
      // Inserts channel docs // Replaces {channel_Docs}
      $this->channel_data = str_replace ("{channel_Docs}", $docs, $this->channel_data);
      
      // Inserts channel skipDays // Replaces {channel_SkipDays}
      $this->channel_data = str_replace ("{channel_SkipDays}", $skipDays, $this->channel_data);
      
      // Inserts channel skipHours // Replaces {channel_SkipHours}
      $this->channel_data = str_replace ("{channel_SkipHours}", $skipHours, $this->channel_data);
      
    }
    
    // Creates image data handler //
    function setImage ($title = "", $src = "", $link = "", $width = "", $height = "", $description = "") {
      
      // Null values become empty values // Start
      $title = ($title == null) ? "" : $title;
      $src = ($src == null) ? "" : $src;
      $link = ($link == null) ? "" : $link;
      $width = ($width == null) ? "" : $width;
      $height = ($height == null) ? "" : $height;
      $description = ($description == null) ? "" : $description;
      // Null values become empty values // End
      
      // Title parser // Convertes quotes and strips backslashes!
      $title = stripslashes (htmlspecialchars (trim ($title), ENT_QUOTES));
      
      // Title parser // Empty titles not allowed
      $title = (empty ($title)) ? "RSS Genesis 1.1" : $title;
      
      // Source parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp://)", $src)) :
      
        $src = "http://rssgenesis.sourceforge.net/Assets/rss.genesis.image.png";
      
      endif;
      
      // Link parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp://)", $link)) :
      
        $link = "http://rssgenesis.sourceforge.net/";
      
      endif;
      
      // Image dimensions parser // Sets dimensions if auto generation is needed
      if (($width == "auto") and ($height == "auto")) :
      
        $dimensions = @getimagesize ($src);
        
        $width = $dimensions[0];
        
        $height = $dimensions[1];
		
		if ($this->rssformat == "RSS20") {
			$this->image_data = str_replace ("\r\n\t\t\t<width>{image_Width}</width>\r\n\t\t\t<height>{image_Height}</height>", "", $this->image_data);
		}
      
      endif;
      
      // Image dimensions parser // Checks integer values
      $width = (is_int ($width)) ? $width : "";
      $height = (is_int ($height)) ? $height : "";
      
      // Description parser // Convertes quotes and strips backslashes!
      $description = stripslashes (htmlspecialchars (trim ($description), ENT_QUOTES));
      
      // Description parser // Empty descriptions not allowed
      $description = (empty ($description)) ? "Powered by: RSS Genesis!" : $description;
      
      // Inserts image title // Replaces {image_Title}
      $this->image_data = str_replace ("{image_Title}", $title, $this->rss_image);
      
      // Inserts image source // Replaces {image_Source}
      $this->image_data = str_replace ("{image_Source}", $src, $this->image_data);
      
      // Inserts image link // Replaces {image_Link}
      $this->image_data = str_replace ("{image_Link}", $link, $this->image_data);
      
      // Inserts image width // Replaces {image_Width}
      $this->image_data = str_replace ("{image_Width}", "$width", $this->image_data);
      
      // Inserts image height // Replaces {image_Height}
      $this->image_data = str_replace ("{image_Height}", "$height", $this->image_data);
      
      // Inserts image description // Replaces {image_Description}
      $this->image_data = str_replace ("{image_Description}", $description, $this->image_data);
      
    }
    
    // Creates item data handler //
    function addItem ($title, $link, $description, $pubdate = "", $category) {
      
      // Null values become empty values // Start
      $title = ($title == null) ? "" : $title;
      $link = ($link == null) ? "" : $link;
      $description = ($description == null) ? "" : $description;
	  $pubdate = ($pubdate == null) ? "" : $pubdate;
	  $category = ($category == null) ? "" : $category;
      // Null values become empty values // End
	  
	  //Formats pubdate
	  $mysqltimestamp = $pubdate;
	  $pubdate = gmdate('D, d M Y H:i:s \G\M\T', strtotime($mysqltimestamp));
      
      // Title parser // Convertes quotes and strips backslashes!
      $title = stripslashes (htmlspecialchars (trim ($title), ENT_QUOTES));
      
      // Title parser // Empty titles not allowed
      if (empty ($title)) :
      
        die ("<font face=\"verdana\" size=\"2\">Error code: <strong>001</strong> - Item's element title is mandatory! - <a href=\"http://rssgenesis.sourceforge.net/index.html#ec001\" target=\"_blank\">Documentation</a></font>");
      
      endif;
      
      // Link parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp(s)?://)", $link)) :
      
        die ("<font face=\"verdana\" size=\"2\">Error code: <strong>002</strong> - Item's element link is mandatory! - <a href=\"http://rssgenesis.sourceforge.net/index.html#ec002\" target=\"_blank\">Documentation</a></font>");
      
      endif;
      
      // Description parser // Convertes quotes and strips backslashes!
      $description = stripslashes (htmlspecialchars (trim ($description), ENT_QUOTES));
	  
	  // Description parser // Convertes quotes and strips backslashes!
      $category = stripslashes (htmlspecialchars (trim ($category), ENT_QUOTES));
      
      // Inserts item title // Replaces {item_Title}
      $temp = str_replace ("{item_Title}", $title, $this->rss_item);
      
      // Inserts item link // Replaces {item_Link}
      $temp = str_replace ("{item_Link}", $link, $temp);
      
      // Inserts item description // Replaces {item_Description}
      $temp = str_replace ("{item_Description}", $description, $temp);
	  
	  //Inserts publication date if feed is RSS20
	  if ($this->rssformat == "RSS20") {
			$temp = str_replace ("{item_PubDate}", $pubdate, $temp);
			
			$temp = str_replace ("{item_category}", $category, $temp);
	  }
      
      // Stores the new added item
      $this->item_data[] = $temp;
      
      // Unsets temporary variable
      unset ($temp);
      
    }
    
    // Creates input data handler //
    function setInput ($title = "", $description = "", $name = "", $link = "") {
      
      // Null values become empty values // Start
      $title = ($title == null) ? "" : $title;
      $description = ($description == null) ? "" : $description;
      $name = ($name == null) ? "" : $name;
      $link = ($link == null) ? "" : $link;
      // Null values become empty values // End
      
      // Title parser // Convertes quotes and strips backslashes!
      $title = stripslashes (htmlspecialchars (trim ($title), ENT_QUOTES));
      
      // Title parser // Empty titles not allowed
      $title = (empty ($title)) ? "Go!" : $title;
      
      // Description parser // Convertes quotes and strips backslashes!
      $description = stripslashes (htmlspecialchars (trim ($description), ENT_QUOTES));
      
      // Description parser // Empty descriptions not allowed
      $description = (empty ($description)) ? "Search:" : $description;
      
      // Name parser // Convertes quotes and strips backslashes!
      $name = stripslashes (htmlspecialchars (trim ($name), ENT_QUOTES));
      
      // Name parser // Empty names not allowed
      $name = (empty ($name)) ? "q" : $name;
      
      // Link parser // Checks absolutes URIs
      if (!preg_match ("(^(ht|f)tp://)", $link)) :
      
        $link = "http://www.google.com/search";
      
      endif;
      
      // Inserts input title // Replaces {input_Title}
      $this->input_data = str_replace ("{input_Title}", $title, $this->rss_input);
      
      // Inserts input description // Replaces {input_Description}
      $this->input_data = str_replace ("{input_Description}", $description, $this->input_data);
      
      // Inserts input name // Replaces {input_Name}
      $this->input_data = str_replace ("{input_Name}", $name, $this->input_data);
      
      // Inserts input link // Replaces {input_Link}
      $this->input_data = str_replace ("{input_Link}", $link, $this->input_data);
      
    }
    
    // Creates function to organize the data on feed //
    function organizeData() {
      
      // Concentrates all stored data in one variable to feed the new RSS feed //
      $this->rss_feed .= $this->rss_header;
      $this->rss_feed .= $this->channel_data;
      $this->rss_feed .= $this->image_data;
      $this->rss_feed .= $this->input_data;
      
      // Storing all itens //
      foreach ($this->item_data as $item) :
      
        $this->rss_feed .= $item;
      
      endforeach;
      
      // Storage continuation //
      $this->rss_feed .= $this->rss_footer;
      
    }
    
    // Creates function to generate the RSS Feed //
    function createFile ($name = "my.rss") {
      
      // Calls the function to organize data before the file creation //
      $this->organizeData();
      
      // Creates the new file //
      $file = @fopen ($name, "w");
      
      // Checks if creation was successful //
      if (!$file) :
      
        die ("<font face=\"verdana\" size=\"2\">Critical Error: <strong>Unable to create: $name</strong></font>");
        
      endif;
      
      // Inserts contents //
      fwrite ($file, $this->rss_feed);
      
      // Ends file creation //
      fclose ($file);
      
      // XML RSS header //
      header ("Content-type: application/rss+xml");
      
      // Display RSS file //
      echo file_get_contents ($name);
      
    }
	
	function getFeed() {
	
		// Calls the function to organize data before the file creation //
		$this->organizeData();
		
		return $this->rss_feed;	
	}
    
  }