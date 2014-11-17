<?php
/*
Plugin Name: HotChalk - CPAWidget
Plugin URI: http://www.hotchalk.com/
Version: 11/05/2014
Author: Zander Fields
Description: This is the first iteration of the CPA Widget for CCNY

This plugin will allow for implementation of the salary widget tool. The salary widget tool allows users to select a local area, state, and job title, and be returned a series of employment statistics based on their selections. All returned data is provided by data downloaded from the Bureau of Labor Statistics.

*/

define ('HC_CPAWIDGET_VERSION', '11/05/2014');
define ('HC_CPAWIDGET_PLUGIN_URL', plugin_dir_url(__FILE__));
define ('HC_CPAWIDGET_PLUGIN_DIR', plugin_dir_path(__FILE__));
define ('HC_CPAWIDGET_SETTINGS_LINK', '<a href="'.home_url().'/wp-admin/admin.php?page=hc-cpawidget">Settings</a>');


class HC_CpaWidget {
	/* define any localized variables here */

	private $myPrivateVars;
	private $opt; /* points to any options defined and used in the admin */

	function __construct() {
		/* Best practice is to save all your settings in 1 array */
		/*   Get this array once and reference throughout plugin */

		$this->opt = get_option('hcCpaWidget');
		
		/* You can do things once here when activating / deactivating, such as creating
		     database tables and deleting them. */

		register_activation_hook(__FILE__,array($this,'activate'));
		register_deactivation_hook( __FILE__,array($this,'deactivate'));
		
		/* Enqueue any scripts needed on the front-end */

		add_action('wp_enqueue_scripts', array($this,'frontScriptEnqueue'));
		
		/* Create all the necessary administration menus. */
		/* Also enqueues scripts and styles used only in the admin */

		add_action('admin_menu', array($this,'adminMenu'));
		
		/* adminInit handles all of the administartion settings  */ 

		add_action('admin_init', array($this,'adminInit'));
		
		// if you need anything in the footer, define it here
		//add_action('wp_footer', array($this,'footerScript'));
		
		$ga_plugin = plugin_basename(__FILE__); 
		
		// this code creates the settings link on the plugins page
		add_filter("plugin_action_links_$ga_plugin", array($this,'pluginSettingsLink'));
		
		// create any shortcodes needed
		add_shortcode( 'hc_cpawidget', array($this,'shortcode'));
    }
	
	// Enqueue any front-end scripts here
	function frontScriptEnqueue() {
		//wp_enqueue_script('swaplogo',HC_PLUGIN_PLUGIN_URL.'js/swaplogo.js',false,null);
		wp_enqueue_style('my_style',HC_CPAWIDGET_PLUGIN_URL.'hc-cpawidget.css');
	}

    /* these admin styles are only loaded when the admin settings page is displayed */
	
	function adminEnqueue() {
		// wp_enqueue_style('hc-plugin-style',HC_PLUGIN_PLUGIN_URL.'css/hc_plugin.css');
	}
	
	// Enqueue any scripts needed in the admin here 
	function adminEnqueueScripts() {
		// wp_enqueue_script('jquery-ui-sortable');
		// wp_enqueue_script('jquery-ui-datepicker');
	}
	
	// code that gets run on plugin activation.
	// create any needed database tables or similar here
	function activate() {
	}

	// code the gets run on plugin de-activation
	// remove any database tables or other settings here
	function deactivate() {
	}
	
	// Setup the admin menu here.  Also enqueues backend styles/scripts
	// images/icon.png is the icon that appears on the admin menu
	function adminMenu() {
		add_menu_page('HotChalk','HotChalk','manage_options','hc_top_menu','',plugin_dir_url(__FILE__).'/images/icon.png', 88.8 ); 
		
		$page = add_submenu_page('hc_top_menu','CpaWidget','CpaWidget','manage_options','hc-cpawidget',array($this,'adminOptionsPage'));
		
		remove_submenu_page('hc_top_menu','hc_top_menu'); // remove extra top level menu item if there
		
		 /* Using registered $page handle to hook stylesheet loading */
	
		add_action( 'admin_print_styles-' . $page, array($this,'adminEnqueue'));
		add_action( 'admin_print_scripts-' . $page, array($this,'adminEnqueueScripts'));
	}
	
	// settings link on plugins page
	function pluginSettingsLink($links) { 
	  $settings_link = HC_CPAWIDGET_SETTINGS_LINK; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	/* Define the settings for your plugin here */ 
	/* Create as many sections as needed */ 

	function adminInit(){
		register_setting( 'hcCpaWidgetOptions', 'hcCpaWidgetOptions', array($this,'optionsValidate'));
		add_settings_section('hcCpaWidgetSection1', 'Plugin Settings Section 1', array($this,'sectionText1'), 'hc-cpawidget');
		add_settings_field('hcCpaWidgetSection1', '', array($this,'section1settings'), 'hc-cpawidget', 'hcCpaWidgetSection1');
	}	

		
	// You can validate input here on saving
	// This gets called when click 'Save Changes' from the admin settings.
	// Process input and then return it
	function optionsValidate($input) {
		return $input;
	}
	
	// Settings section description
	function sectionText1() {
		?>
        <p>This plugin will allow for implementation of the salary widget tool. The salary widget tool allows users to select a local area, state, and job title, and be returned a series of employment statistics based on their selections. All returned data is provided by data downloaded from the Bureau of Labor Statistics.</p>
        <?php
	}
	
	// Example setting in admin
	/*
	function section1settings() {
		echo '<div class="section1">';
	    echo '<label>Setting 1 </label><input type="text" name="hcSalaryWidgetOptions[setting1]" value="'.$this->opt['setting1'].'" />';
		echo '</div>';
	}
	*/
	// Example shortcode
	// [hc_plugin parm1="parm1_setting"]

	function shortcode( ) {
		
		ob_start(); ?>
		<script language="javascript" type="text/javascript">

			function getXMLHTTP() { //function to return the xml http object
				var xmlhttp=false;	
				try{
					xmlhttp=new XMLHttpRequest();
				}
				catch(e)	{		
					try{			
						xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch(e){
						try{
						xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
						}
						catch(e1){
							xmlhttp=false;
						}
					}
				}
		 	
				return xmlhttp;
		    }
	
				function getData(stateId) 
			{		
				
					$( "#dialog_trigger" ).hide();
					
					//Clean out data section on new select
					document.getElementById('datadiv').innerHTML="";
					
			
					var strURL="http://hczander.wpengine.com/wp-content/plugins/hc-cpawidget/handler.php?state="+stateId;
					var req = getXMLHTTP();
			
					if (req) {
				
						req.onreadystatechange = function() {
							if (req.readyState == 4) {
								// only if "OK"
								if (req.status == 200) {	
													
									document.getElementById('datadiv').innerHTML=req.responseText;											
								} else {
									alert("There was a problem while using XMLHTTP:\n" + req.statusText);
								}
							}				
						}			
						req.open("GET", strURL, true);
						req.send(null);
				
				}	
				
		
			}
			
			function getLic(stateId) 
		{		
				
				//Clean out dialog section on new select
				document.getElementById('dialog').innerHTML="";
				
		
				var strURL="http://hczander.wpengine.com/wp-content/plugins/hc-cpawidget/licHandler.php?state="+stateId;
				var req = getXMLHTTP();
		
				if (req) {
			
					req.onreadystatechange = function() {
						if (req.readyState == 4) {
							// only if "OK"
							if (req.status == 200) {	
												
								document.getElementById('dialog').innerHTML=req.responseText;											
							} else {
								alert("There was a problem while using XMLHTTP:\n" + req.statusText);
							}
						}				
					}			
					req.open("GET", strURL, true);
					req.send(null);
			
			}	
			
	
		}
				
		</script>
		<?php
		
	
		try 
		{
			$db = new PDO("mysql:host=censored;dbname=censored","censored","censored");
		}	catch (Exception $e) 
			{
				echo "Could not connect to database.";
				exit;
			}
		
		$stmt = $db->prepare("SELECT DISTINCT STATE 
				FROM CPA_db");
	
	
		?>

					<div class="cpa-widget"><h2>CPA Widget</h2>
					<form>
					    <div class="selection"><p class="title">Select a State</p>
							<select id="selectstate" class="required" name="state" onChange="getData(this.value);getLic(this.value)">
						
						<option value="">Select a State</option>
						<? if($stmt->execute()) { 
						while($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
						<option value='<?=$rows['STATE']?>'><?=$rows['STATE']?></option>
						<? } } ?>
						</select></div>						

						<div class="output">
                            <div id="datadiv"></div>
                        </div>
				</form>
 				<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
 					<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
 					<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

 						<button id="dialog_trigger">See CPA Licensure Requirements</button>
 						<div id="dialog" style="display:none;" title="CPA Licensure Requirements"><p>Hello World</p></div>
 						<script>
						$( "#dialog_trigger" ).hide();
						$("#selectstate").click(function() {
							$("#dialog_trigger").toggle();
						});
 						$( "#dialog_trigger" ).click(function() {
 						$( "#dialog" ).dialog( "open" );
 						});
 						$("#dialog").dialog({
 						    autoOpen: false,
 						    position: 'center' ,
 						    title: 'CPA Licensure Requirements',
 						    draggable: false,
 						    width : 586,
 						    height : 632, 
 						    resizable : true,
 						    modal : true,
 						});


 						</script>
				
				<p>** Indicates potential further requirements or addendums</p>
			</div>
			<?php 
	
			return ob_get_clean(); 
		}
	
		// footer scripts		
		function footerScript () {
			?>
			<script type="text/javascript">
			// any needed javascript code here - goes in footer
	        </script>
	        <?php
		}
	
		/* the Settings page for this plugin */
	
		function adminOptionsPage() { ?>
			<div id="hc_cpawidget">
			<h2>(CPAWidget) - HotChalk, Inc. v<?php echo HC_CPAWIDGET_VERSION; ?></h2>
			<form method="post" action="options.php">
			<?php settings_fields('hcCpaWidgetOptions'); ?>
			<?php do_settings_sections('hc-cpawidget'); ?>
			</form></div>
			<?php
		}
	}

	$hcPlugin = new HC_CpaWidget();
	?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		