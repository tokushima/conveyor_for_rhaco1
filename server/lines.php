<?php
/**
 * @author SHIGETA Takeshiro
 * 
 * @license New BSD License
 * @copyright Copyright 2006- The Rhacophorus Project. All rights reserved.
 */
require_once("__init__.php");
Rhaco::import("Conveyaml");
$path = FileUtil::path(Rhaco::constant("PUBLISH_PATH"));

Conveyaml::execute(
<<< __YAML__
---
plugins: 
  - 
    module: SubscriptionFilelist.SubscriptionFilelist
    config: 
      path: $path
      recursive: 0
      extension: .php
  - 
    module: FilterRegex.FilterRegex
    config: 
      mode: replace
      property: link
      search: $path
      replace: 
  - 
    module: FilterSort.FilterSort
    config:
      property: pubDate
      order: descend
  - 
    module: FeedOut.FeedOut
__YAML__
);

?>