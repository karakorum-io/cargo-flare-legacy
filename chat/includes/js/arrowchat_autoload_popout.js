function loadScript(url,callback){var head=document.getElementsByTagName('head')[0];var script=document.createElement('script');script.type='text/javascript';script.src=url;script.charset='utf-8';script.onreadystatechange=callback;script.onload=callback;head.appendChild(script)}function loadCSS(url,callback){var head=document.getElementsByTagName('head')[0];var script=document.createElement('link');script.type='text/css';script.rel='stylesheet';script.media='all';script.id='arrowchat_css';script.href=url;script.charset='utf-8';script.onreadystatechange=callback;script.onload=callback;head.appendChild(script)}var JSLoaded=function(){};var DJSLoaded=function(){loadScript("<?php echo $base_url; ?>external.php?type=pjs&v=<?php echo $admin_update_time; ?>",JSLoaded)};var jqueryUILoaded=function(){loadScript("<?php echo $base_url; ?>external.php?type=djs",DJSLoaded)};var jqueryLoaded=function(){loadScript("<?php echo $base_url; echo AC_FOLDER_INCLUDES; ?>/js/jquery-ui.js",jqueryUILoaded)};var CSSLoaded=function(){<?php if($enable_jquery==1&&$enable_jquery_ui==1){ ?>loadScript("<?php echo $base_url; echo AC_FOLDER_INCLUDES; ?>/js/jquery.js",jqueryLoaded);<?php }else if($enable_jquery==0&&$enable_jquery_ui==1){ ?>loadScript("<?php echo $base_url; echo AC_FOLDER_INCLUDES; ?>/js/jquery-ui.js",jqueryUILoaded);<?php }else if($enable_jquery==1&&$enable_jquery_ui==0){ ?>loadScript("<?php echo $base_url; echo AC_FOLDER_INCLUDES; ?>/js/jquery.js",jqueryUILoaded);<?php }else{ ?>loadScript("<?php echo $base_url; ?>external.php?type=djs",DJSLoaded);<?php } ?>};loadCSS("<?php echo $base_url; ?>external.php?type=popoutcss&v=<?php echo $admin_update_time; ?>",CSSLoaded);