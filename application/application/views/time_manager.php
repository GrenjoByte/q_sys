<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0">
	<title>KidsPlayZone</title>
	<style>
		.floater-button{
	        position: fixed !important;
	        bottom: 30px !important;
	        right: 30px !important;
	        z-index: 1000 !important;
	    }
	    .invisible {
			display: none !important;
		}

		.image-container {
		    aspect-ratio: 5 / 4;
		    overflow: hidden;
		}

		.image-container img {
		    width: 100%;
		    height: 100%;
		    object-fit: cover; /* Ensures the image fills the area while keeping its aspect ratio */
		}
		body.no_scroll {
		    overflow: hidden !important;
		    height: 100% !important;   /* Prevent vertical scrolling */
		    width: 100% !important;    /* Prevent horizontal scrolling */
		    position: fixed !important; /* Locks the viewport in place */
		}

		#loading_overlay {
		    position: fixed;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(50, 50, 50, 0.85); /* Grey overlay */
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    z-index: 2147483647 !important;
		    pointer-events: auto; /* Blocks interaction */
		}

		.loading_text {
		    font-size: 1.5rem;
		    font-weight: bold;
		    color: #f1f1f1; /* Light grey/white text for contrast */
		}
		.break-text {
			word-wrap: break-word;
			word-break: break-word;
			white-space: normal;
			overflow-wrap: break-word;
		}
		.no-break {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		

		/* Dropdown item layout and truncation */
		.ui.dropdown .menu > .item {
			display: flex !important;
			align-items: center !important;
			gap: 8px !important;
			overflow: hidden !important;
		}

		/* Image inside dropdown */
		.ui.dropdown .menu > .item img.item_avatar {
			width: 30px !important;
			height: 30px !important;
			object-fit: cover !important;
			flex-shrink: 0 !important;
			border-radius: 4px;
		}

		/* Text inside dropdown item */
		.ui.dropdown .menu > .item .text,
		.ui.dropdown .menu > .item span {
			flex: 1 1 auto;
			min-width: 0;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			display: block;
		}

		/* Fix ellipsis in the selected item display area */
		.ui.selection.dropdown > .text {
			display: flex !important;
			align-items: center !important;
			gap: 6px !important;
			white-space: nowrap !important;
			overflow: hidden !important;
			text-overflow: ellipsis !important;
			min-width: 0 !important;
		}

		/* Selected item image */
		.ui.selection.dropdown > .text img {
			width: 24px;
			height: 24px;
			object-fit: cover;
			flex-shrink: 0;
			border-radius: 3px;
		}

		/* 5:4 Avatar for item thumbnails */
		.ui.item_avatar,
			img.ui.item_avatar {
	  		display: inline-block;
	  		vertical-align: middle;
	  		overflow: hidden;
	  		border-radius: 5px;            /* adjust if you want more/less radius */
	  		background: #f0f0f0;          /* fallback background */
	  		aspect-ratio: 5 / 4;          /* enforces 5:4 ratio */
	  		width: 70px;                  /* default size - adjust as needed */
	  		height: auto;
		}

		/* Makes sure <img> inside scales properly */
		.ui.item_avatar img {
		  	width: 100%;
		  	height: 100%;
		  	object-fit: cover;            /* prevents distortion */
		  	display: block;
		}

		/* Optional sizes */
		.ui.item_avatar.small  { width: 50px; }
		.ui.item_avatar.medium { width: 80px; }
		.ui.item_avatar.large  { width: 120px; }

		/* Optional: soft shadow like Semantic image */
		.ui.item_avatar { box-shadow: 0 1px 2px rgba(0,0,0,0.1); }

		/* ---- Fallback for old browsers without aspect-ratio ---- */
		@supports not (aspect-ratio: 1/1) {
		  	.ui.item_avatar {
		    	position: relative;
		    	width: 70px;
		    	padding-top: calc(100% * (4/5)); /* keeps 5:4 ratio */
		    	height: 0;
		  	}
		  	.ui.item_avatar img {
			    position: absolute;
			    top: 0; left: 0;
			    width: 100%;
		    	height: 100%;
		    	object-fit: cover;
		  	}
		}

		canvas {
			display: none;
			width: 100%;
			max-width: 100%;      /* adjust to fit modal width */
			height: auto;
			object-fit: cover;
			overflow: hidden;
			box-sizing: border-box;
		}
		video {
			width: 100%;
			max-width: 100%;      /* adjust to fit modal width */
			height: auto;
			object-fit: cover;
			overflow: hidden;
			box-sizing: border-box;
		}

		/*html {
		    transform: scale(0.9);
		    transform-origin: top left;
		    width: 111%;
		}*/

	    
	</style>
</head>
<?php include 'esses/notifications.php';?>
<div class="ui sidebar vertical inverted menu" id="sidebar_menu">
	<a class="item">
		Menu
	</a>
	<a class="item">
		Application
	</a>
</div>
<div class="pusher">
	<body onload="check_active_tab()">
		<header>
			<?php include 'esses/nav.php';?>
		</header>
		<main>
			<div class="ui center aligned icon header transition hidden" id="loading_overlay">
			  	<x class="loading_text">
			  		<i class="cog loading icon"></i>
			  		Loading
			  	</x>
			</div>
			<div class="ui column grid centered">
				<div class="fourteen wide mobile fourteen wide tablet twelve wide computer column centered">
					<div class="ui secondary labeled icon pointing tabular menu fluid item three">
						<a class="gray item" data-tab="time_manager_tab" id="time_manager_tab">
							<h3 class="ui header center aligned pointered">Playtime Manager</h3>
						</a>
						<a class="active gray item" data-tab="pos_tab" id="pos_tab">
							<h3 class="ui header center aligned pointered">Sales</h3>
						</a>
						<a class="gray item" data-tab="supply_tab" id="supply_tab">
							<h3 class="ui header center aligned pointered">Inventory</h3>
						</a>	
					</div>
					<div class="ui bottom attached tab" data-tab="time_manager_tab">
						<div class="ui transition fifteen wide column centered" id="time_manager_section">
							<h2 class="ui teal header big center aligned pointered time_manager_header">
			                    Active Playtimes
			                </h2>
			                <!-- <div class="ui right aligned segment">
			                	<div class="ui right aligned category search">
									<div class="ui icon input">
										<input class="prompt" type="text" placeholder="Search...">
										<i class="search icon"></i>
									</div>
									<div class="results"></div>
								</div>	
			                </div> -->
						    <div class="ui five doubling special cards" id="times_container">
						    </div>
						    <div class="ui centered grid" id="empty_message">
					            <div class="sixteen wide column">
					                <h1 class="ui grey header huge center aligned">
					                	<br>
					                    Empty
					                </h1>
					            </div>
					        </div>
						</div>
					</div>
					<div class="ui bottom attached tab active" data-tab="pos_tab">
						<div class="ui secondary menu">
							<div class="right menu">
								<div class="ui search item" id="pos_inventory_search">
									<div class="ui icon input">
										<input class="prompt" type="text" placeholder="Search...">
										<i class="search link icon"></i>
									</div>
									<div class="results"></div>
								</div>
								<div class="ui item" tabindex="0">
									<i class="icons large link" id="pos_checkout_cart_activator" data-tooltip="Sales Checkout" data-position="bottom left" data-variation="mini">
										<i class="shopping cart icon blue link"></i>
										<div class="floating ui mini orange label transition hidden" id="pos_checkout_cart_counter">0</div>
									</i>
								</div>
							</div>
						</div>
						<br>
						<div class="ui five doubling special cards" id="pos_items_container">
						</div>
						<div class="ui centered grid" id="pos_empty_message">
				            <div class="sixteen wide column">
				                <h1 class="ui grey header huge center aligned">
				                	<br>
				                    Empty
				                </h1>
				            </div>
				        </div>
					</div>	
					<div class="ui bottom attached tab" data-tab="supply_tab">
						<div class="ui secondary menu">
							<div class="right menu">
								<div class="ui search item" id="supply_inventory_search">
									<div class="ui icon input">
										<input class="prompt" type="text" placeholder="Search...">
										<i class="search link icon"></i>
									</div>
									<div class="results"></div>
								</div>
								<div class="ui item" tabindex="0">
									<i class="icons large link" id="supply_checkout_cart_activator" data-tooltip="Supply Checkout" data-position="bottom left" data-variation="mini">
										<i class="shopping cart icon green link"></i>
										<div class="floating ui mini orange label transition hidden" id="supply_checkout_cart_counter">0</div>
									</i>
								</div>
							</div>
						</div>
						<br>
						<div class="ui six doubling special cards" id="supply_items_container">
						</div>
						<div class="ui centered grid" id="supply_empty_message">
				            <div class="sixteen wide column">
				                <h1 class="ui grey header huge center aligned">
				                	<br>
				                    Empty
				                </h1>
				            </div>
				        </div>
					</div>  
				</div>
				<div class="column row centered">
						
				</div>
			</div>	
			<div class="ui mini modal" id="profile_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="profile_header">Child Profile Creation</a>
		        </div>
		        <div class="scrolling content">
		        	<form class="ui padded basic segment form" id="signup_form">
		        		<div class="required field">
					        <label>Guardian's Name</label>
					        <input type="text" name="guardian_name" placeholder="e.g. Juan A. Cruz">
					    </div>
		        		<div class="required field">
					        <label>Guardian's Contact Number</label>
					        <input type="text" name="guardian_contact" placeholder="09**-***-****">
					    </div>
					    <div class="required field">
					        <label>Child's Full Name</label>
					        <input type="text" name="full_name" placeholder="e.g. Juan A. Cruz">
					    </div>
					    <div class="required field">
					        <label>Child's Gender</label>
					        <div class="ui icon selection dropdown button basic small" id="gender_drop">
					            <i class="venus mars icon"></i>&emsp;
					            <input type="hidden" name="gender" id="gender">
					            <div class="default text">Child's Gender</div>
					            <div class="menu">
					                <div class="item" data-value="M">Male <i class="mars icon"></i></div>
					                <div class="item" data-value="F">Female <i class="venus icon"></i></div>
					            </div>
					        </div>
					    </div>
					    <div class="required field">
					        <label>Child's Birthdate</label>
					        <input type="date" name="birthdate" placeholder="YYYY-MM-DD">
					    </div>
					    <!-- Webcam Section -->
					    <div class="required field">
						    <label> Child's Profile Image</label>
			                <input class="invisible" type="text" readonly name="profile_image_name" id="profile_image_name" value="">

			                <div class="ui segment center aligned">
							    <video id="camera_stream" autoplay></video>
							    <canvas id="captured_canvas" style="display:none;"></canvas>
							    <div class="ui mini buttons">
							        <button type="button" id="capture_button" class="ui teal button">Capture</button>
							        <button type="button" id="retake_button" class="ui yellow button" style="display:none;">Retake</button>
							    </div>
							</div>
						</div>
					    <!-- Hidden field to store base64 image -->
					    <input type="hidden" name="profile_image" id="profile_image">
					</form>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
				    <button class="ui button green small button" form="signup_form" type="submit">
				    	Confirm
				    </button>
		        </div>
		    </div>
		    <div class="ui mini modal" id="profile_update_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="profile_update_header">Update Profile</a>
			    </div>
			    <div class="scrolling content">
			        <form class="ui padded basic segment form" id="update_form">
			        	<div class="required field">
					        <label>Guardian's Name</label>
					        <input type="text" name="update_guardian_name" id="update_guardian_name" placeholder="e.g. Juan A. Cruz">
					    </div>
			            <div class="required field">
			                <label>Guardian's Contact Number</label>
			                <input type="text" name="update_guardian_contact" id="update_guardian_contact" placeholder="09**-***-****">
			            </div>
			            <div class="required field">
			                <label>Child's Full Name</label>
			                <input type="text" name="update_full_name" id="update_full_name" placeholder="e.g. Juan A. Cruz">
			            </div>
			            <div class="required field">
			                <label>Child's Gender</label>
			                <div class="ui icon selection dropdown button basic small" id="update_gender_drop">
			                    <i class="venus mars icon"></i>&emsp;
			                    <input type="hidden" name="update_gender" id="update_gender">
			                    <div class="default text">Child's Gender</div>
			                    <div class="menu">
			                        <div class="item" data-value="M">Male <i class="mars icon"></i></div>
			                        <div class="item" data-value="F">Female <i class="venus icon"></i></div>
			                    </div>
			                </div>
			            </div>
			            <div class="required field">
			                <label>Child's Birthdate</label>
			                <input type="date" name="update_birthdate" id="update_birthdate" placeholder="YYYY-MM-DD">
			            </div>

			            <!-- Current Image Section -->
			            <div class="field" id="current_image_field" style="text-align:center;">
		                	<label>Current Profile Image</label>
			                <div class="ui segment center aligned">
			                    <img id="current_profile_image" 
			                         src="" 
			                         alt="Current Profile Image"
			                         style="max-width:100%; aspect-ratio:5/4; object-fit:cover;"/>
				                <div class="ui labeled green mini icon button" id="update_image_button">
			                        <i class="redo icon"></i>
			                        Retake Image
			                    </div>
		                    </div>
			            </div>

			            <!-- Webcam Section -->
			            <div class="field invisible" id="update_webcam_field" style="text-align:center;">
		                	<label>Replace Profile Image (Optional)</label>
			                <input class="invisible" type="text" readonly name="update_profile_image_name" id="update_profile_image_name" value="">

			                <div class="ui segment center aligned">
							    <video id="update_camera_stream" autoplay></video>
							    <canvas id="update_captured_canvas" style="display:none;"></canvas>
							    <div class="ui mini buttons">
							        <button type="button" id="update_capture_button" class="ui teal button">Capture</button>
							        <button type="button" id="update_retake_button" class="ui yellow button" style="display:none;">Retake</button>
							        <button type="button" id="cancel_image_update" class="ui orange button">Cancel</button>
							    </div>
							</div>
			            </div>

			            <!-- Hidden field to store base64 -->
					    <input type="hidden" id="update_client_id" name="update_client_id">
					    <input type="hidden" id="update_profile_image" name="update_profile_image">
			        </form>
			    </div>
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui button green small button" form="update_form" type="submit">
			            Save Changes
			        </button>
			    </div>
			</div>

		    <div class="ui mini modal" id="new_client_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="new_client_header">Time Profile</a>
		        </div>
		        <div class="content">
		        	<form class="ui padded basic segment form" id="new_client_form">
		        		<div class="required field">
	                        <label>Child's Name</label>
	                        <div class="ui icon selection dropdown button basic small" id="new_client_drop">
	                            <span class="transition" id="new_client_icon"><i class="user icon"></i>&nbsp;&nbsp;</span>
	                            <input type="hidden" name="client_id" id="client_id" value="">
	                            <div class="default text">Child's Name</div>
	                            <div class="menu" id="new_client_drop_menu">
	                                
	                            </div>
	                        </div>
	                    </div>
	                    <div class="required field">
	                        <label>Time</label>
	                        <div class="ui icon selection dropdown button basic small" id="time_drop">
	                            <span class="transition" id="time_icon"><i class="clock icon"></i>&nbsp;&nbsp;</span>
	                            <input type="hidden" name="time_rate" id="time_rate" value="">
	                            <div class="default text">Time</div>
	                            <div class="menu" id="time_drop_menu">
	                                
	                            </div>
	                        </div>
	                    </div>
					</form>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
				    <button class="ui button green small button" form="new_client_form" type="submit">
				    	Confirm
				    </button>
		        </div>
		    </div>
		    <div class="ui mini modal" id="extend_time_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="extend_time_header">Extend Time</a>
		        </div>
		        <div class="content">
		        	<form class="ui padded basic segment form" id="extend_time_form">
		        		<div class="required field">
	                        <label>Child's Name</label>
	                        <div class="ui icon selection dropdown button basic small" id="extend_client_drop">
	                            <input type="hidden" name="extend_client_id" id="extend_client_id" value="">
	                            <div class="default text">Child's Name</div>
	                            <div class="menu" id="extend_client_drop_menu">
	                                
	                            </div>
	                        </div>
	                    </div>
	                    <div class="required field">
	                        <label>Time</label>
	                        <div class="ui icon selection dropdown button basic small" id="extend_time_drop">
	                            <span class="transition" id="extend_time_icon"><i class="clock icon"></i>&nbsp;&nbsp;</span>
	                            <input type="hidden" name="extend_time_rate" id="extend_time_rate" value="">
	                            <div class="default text">Time</div>
	                            <div class="menu" id="extend_time_drop_menu">
	                                
	                            </div>
	                        </div>
	                    </div>
					</form>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
				    <button class="ui button green small button" form="extend_time_form" type="submit">
				    	Confirm
				    </button>
		        </div>
		    </div>
		    <div class="ui small modal" id="time_reports_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="reports_header">Reports</a>
		        </div>
		        <div class="content">
		        	<div class="ui form">
					    <div class="fields">
					        <!-- Report Type Dropdown -->
					        <div class="field">
					            <label>Report Type</label>
					            <div class="ui selection dropdown" id="report_type_dropdown">
					                <input type="hidden" name="report_type" id="report_type">
					                <i class="dropdown icon"></i>
					                <div class="default text">Select Report Type</div>
					                <div class="menu">
					                    <div class="item" data-value="daily">Daily</div>
					                    <div class="item" data-value="monthly">Monthly</div>
					                    <div class="item" data-value="annual">Annually</div>
					                </div>
					            </div>
					        </div>

					        <!-- Report Date Button -->
					        <div class="field">
							    <label>Report Date</label>
							    <input type="date" name="report_date" id="report_date" placeholder="Report Date" value="<?= date('Y-m-d'); ?>">
						  	</div>
					    </div>
					</div>
				</div>
				<div class="ui fitted divider"></div>
		        <div class="scrolling content">
		        	<table class="ui selectable sortable teal table transition hidden tm_report_table" id="daily_report_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Time</th>
								<th>Rate</th>
							</tr>
						</thead>
						<tbody id="daily_report">
							
						</tbody>
						<tfoot>
							<tr>
								<td></td>
								<td></td>
								<td id="daily_reports_total_rate"></td>
							</tr>
						</tfoot>
					</table>
		        	<table class="ui selectable sortable teal table transition hidden tm_report_table" id="monthly_report_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Time</th>
								<th>Rate</th>
							</tr>
						</thead>
						<tbody id="monthly_report">
							
						</tbody>
						<tfoot>
							<tr>
								<td></td>
								<td></td>
								<td id="monthly_reports_total_rate"></td>
							</tr>
						</tfoot>
					</table>
		        	<table class="ui selectable sortable teal table transition hidden tm_report_table" id="annual_report_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Time</th>
								<th>Rate</th>
							</tr>
						</thead>
						<tbody id="annual_report">
							
						</tbody>
						<tfoot>
							<tr>
								<td></td>
								<td></td>
								<td id="annual_reports_total_rate"></td>
							</tr>
						</tfoot>
					</table>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		        </div>
		    </div>

		    <div class="ui small modal" id="time_logs_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="logs_header">Logs</a>
		        </div>
		        <div class="content">
		        	<div class="ui form">
					    <div class="fields">
					        <!-- Report Type Dropdown -->
					        <div class="field">
					            <label>Log Type</label>
					            <div class="ui selection dropdown" id="log_type_dropdown">
					                <input type="hidden" name="log_type" id="log_type">
					                <i class="dropdown icon"></i>
					                <div class="default text">Select Log Type</div>
					                <div class="menu">
					                    <div class="item" data-value="daily">Daily</div>
					                    <div class="item" data-value="monthly">Monthly</div>
					                    <div class="item" data-value="annual">Annually</div>
					                </div>
					            </div>
					        </div>

					        <!-- log Date Button -->
					        <div class="field">
							    <label>Log Date</label>
							    <input type="date" name="log_date" id="log_date" placeholder="log Date" value="<?= date('Y-m-d'); ?>">
						  	</div>
					    </div>
					</div>
				</div>
				<div class="ui fitted divider"></div>
		        <div class="scrolling content">
					<table class="ui selectable sortable teal table transition hidden tm_log_table" id="daily_log_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Activity</th>
								<th class="sorted descending">Timestamp</th>
							</tr>
						</thead>
						<tbody id="daily_log">
							
						</tbody>
					</table>
					<table class="ui selectable sortable teal table transition hidden tm_log_table" id="monthly_log_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Activity</th>
								<th class="sorted descending">Timestamp</th>
							</tr>
						</thead>
						<tbody id="monthly_log">
							
						</tbody>
					</table>
					<table class="ui selectable sortable teal table transition hidden tm_log_table" id="annual_log_table">
						<thead>
							<tr>
								<th>Child's Name</th>
								<th>Activity</th>
								<th class="sorted descending">Timestamp</th>
							</tr>
						</thead>
						<tbody id="annual_log">
							
						</tbody>
					</table>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		        </div>
		    </div>

		    <div class="ui mini modal" id="new_pos_item_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="new_client_header">New Item</a>
			    </div>
			    <div class="scrolling content">
			        <form class="ui padded basic segment form" id="new_pos_item_form">
			            <div class="required field">
			                <label>Item Name</label>
			                <input type="text" name="new_pos_item_name" id="new_pos_item_name" placeholder="e.g. Teddy Bear - Pink">
			            </div>
			            <div class="required field">
			                <label>Item Price</label>
			                <input type="text" name="new_pos_item_price" id="new_pos_item_price" placeholder="₱">
			            </div>
			            <div class="required field">
			                <label>Item Unit (Singular)</label>
			                <input type="text" name="new_pos_item_unit" id="new_pos_item_unit" placeholder="e.g. piece, box">
			            </div>
			            <div class="required field">
			                <label>Initial Stock</label>
			                <input type="text" name="new_pos_item_stock" id="new_pos_item_stock" placeholder="Initial Stock">
			            </div>
			            <div class="required field">
			                <label>Low Stock Level</label>
			                <input type="text" name="new_pos_item_low" id="new_pos_item_low" placeholder="Initial Stock">
			            </div>
			            <div class="required field">
			                <label>Item Image</label>
			                <input class="invisible" type="text" readonly name="new_pos_item_image_name" id="new_pos_item_image_name" value="">
			                
			                <div class="ui segment center aligned">
			                    <video id="new_pos_camera_stream" autoplay></video>
			                    <canvas id="new_pos_captured_canvas" style="display:none;"></canvas>
			                    <img id="new_pos_uploaded_preview" style="aspect-ratio: 5 / 4; display:none; width:100%; height:auto; object-fit:cover;">

			                    <!-- Hidden input for file upload -->
			                    <input type="file" id="new_pos_image_file" accept="image/*" style="display:none;">

			                    <div class="ui mini buttons">
			                        <button type="button" id="new_pos_capture_button" class="ui teal button" style="display:none;">Capture</button>
			                        <button type="button" id="new_pos_retake_button" class="ui yellow button" style="display:none;">Retake</button>
			                    </div>
			                </div>
			            </div>
			            <input type="hidden" id="new_pos_item_image" name="new_pos_item_image">
			        </form>
			    </div>
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui button green small button" form="new_pos_item_form" type="submit">
			            Confirm
			        </button>
			    </div>
			</div>
			<div class="ui mini modal" id="update_pos_item_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="update_client_header">Edit Item</a>
			    </div>
			    <div class="scrolling content">
			        <form class="ui padded basic segment form" id="update_pos_item_form">
					    <!-- Hidden ID for updating -->
					    <input type="hidden" name="update_pos_item_id" id="update_pos_item_id">

					    <div class="required field">
					        <label>Item Name</label>
					        <input type="text" name="update_pos_item_name" id="update_pos_item_name" placeholder="e.g. Teddy Bear - Pink">
					    </div>
					    <div class="required field">
					        <label>Item Price</label>
					        <input type="text" name="update_pos_item_price" id="update_pos_item_price" placeholder="₱">
					    </div>
					    <div class="required field">
					        <label>Item Unit (Singular)</label>
					        <input type="text" name="update_pos_item_unit" id="update_pos_item_unit" placeholder="e.g. piece, box">
					    </div>
					    <div class="required field">
					        <label>Current Stock</label>
					        <input type="text" name="update_pos_item_stock" id="update_pos_item_stock" placeholder="Initial Stock">
					    </div>
					    <div class="required field">
					        <label>Low Stock Level</label>
					        <input type="text" name="update_pos_item_low" id="update_pos_item_low" placeholder="Initial Stock">
					    </div>

					    <!-- Current Image Section -->
					    <div class="field" id="current_pos_image_field" style="text-align:center;">
					        <label>Current Item Image</label>
					        <div class="ui segment center aligned">
					            <img id="current_pos_item_image" 
					                 src="" 
					                 alt="Current Item Image"
					                 style="max-width:100%; aspect-ratio:5/4; object-fit:cover;">
					            <div class="ui labeled green mini icon button" id="update_pos_item_image_button">
					                <i class="redo icon"></i>
					                Retake Image
					            </div>
					        </div>
					    </div>

					    <!-- Webcam Section -->
					    <div class="field invisible" id="update_pos_webcam_field" style="text-align:center;">
					        <label>Replace Item Image (Optional)</label>
					        <input class="invisible" type="text" readonly name="update_pos_item_image_name" id="update_pos_item_image_name" value="">

					        <div class="ui segment center aligned">
					            <video id="update_pos_camera_stream" autoplay></video>
					            <canvas id="update_pos_captured_canvas" style="display:none;"></canvas>
					            <img id="update_pos_uploaded_preview" style="aspect-ratio: 5 / 4; display:none; width:100%; height:auto; object-fit:cover;">
					            
					            <input type="file" id="update_pos_image_file" accept="image/*" style="display:none;">
					            <div class="ui mini buttons">
					                <button type="button" id="update_pos_capture_button" class="ui teal button">Capture</button>
					                <button type="button" id="update_pos_retake_button" class="ui yellow button" style="display:none;">Retake</button>
					                <button type="button" id="cancel_pos_image_update" class="ui orange button">Cancel</button>
					            </div>
					        </div>
					    </div>

					    <input type="hidden" id="update_pos_item_image" name="update_pos_item_image">
					</form>

			    </div>
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui button green small button" form="update_pos_item_form" type="submit">
			            Confirm
			        </button>
			    </div>
			</div>

			<div class="ui tiny modal" id="pos_checkout_cart_modal">
		        <div class="ui header center aligned">
		            <a class="break-text">Sales Checkout</a>
		        </div>
		        <div class="scrolling content">
		        	<div class="ui centered grid transition" id="pos_checkout_cart_empty_message">
			            <div class="sixteen wide column">
			                <h2 class="ui grey header huge center aligned">
			                    Empty
			                </h2>
			            </div>
			        </div>
		        	<div class="ui relaxed divided list" id="pos_checkout_cart_content">
						
					</div>
				</div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		            <button class="ui button green small button" id="pos_checkout_submit">
			            Checkout
			        </button>
		        </div>
		    </div>

		    <div class="ui small modal" id="pos_checkouts_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="pos_checkouts_header">Sales Records</a>
			    </div>

			    <div class="content">
			        <div class="ui form">
			            <div class="fields">
			                <div class="field">
			                    <label>Report Type</label>
			                    <div class="ui selection dropdown" id="pos_checkouts_type_dropdown">
			                        <input type="hidden" name="pos_checkouts_type" id="pos_checkouts_type">
			                        <i class="dropdown icon"></i>
			                        <div class="default text">Select Report Type</div>
			                        <div class="menu">
			                            <div class="item" data-value="daily">Daily</div>
			                            <div class="item" data-value="monthly">Monthly</div>
			                            <div class="item" data-value="annual">Annually</div>
			                        </div>
			                    </div>
			                </div>

			                <div class="field">
			                    <label>Sales Date</label>
			                    <input type="date" name="pos_checkouts_date" id="pos_checkouts_date" placeholder="Sales Date" value="<?= date('Y-m-d'); ?>">
			                </div>
			            </div>
			        </div>
			    </div>

			    <div class="ui fitted divider"></div>

			    <div class="scrolling content">
			        <!-- Daily Table -->
			        <table class="ui selectable sortable teal table transition hidden pos_checkouts_table" id="daily_pos_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="pos_checkouts_tbody" id="daily_pos_checkouts"></tbody>
			        </table>

			        <!-- Monthly Table -->
			        <table class="ui selectable sortable teal table transition hidden pos_checkouts_table" id="monthly_pos_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="pos_checkouts_tbody" id="monthly_pos_checkouts"></tbody>
			        </table>

			        <!-- Annual Table -->
			        <table class="ui selectable sortable teal table transition hidden pos_checkouts_table" id="annual_pos_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="pos_checkouts_tbody" id="annual_pos_checkouts"></tbody>
			        </table>
			    </div>

			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			    </div>
			</div>

			<div class="ui small modal" id="pos_restocking_modal">
			    <div class="ui header center aligned">
			        <a class="break-text">Sales Restocking</a>
			    </div>

			    <div class="content">
			        <!-- Tab Menu -->
					<div class="ui secondary labeled icon pointing tabular menu fluid item two">
			            <a class="item active" data-tab="form_tab" id="restock_items">Restock Items</a>
			            <a class="item" data-tab="records_tab" id="records_tab">Restocking Records</a>
			        </div>

			        <!-- === RESTOCKING FORM TAB === -->
			        <div class="ui active tab" data-tab="form_tab">
			            <form class="ui tiny form" id="pos_restocking_form">
			                <div class="fields">
			                    <div class="six wide required pos_restocking_date_field field">
			                        <label>Restocking Date</label>
			                        <input type="date" name="pos_restocking_date" id="pos_restocking_date" required placeholder="Restocking Date" value="<?= date('Y-m-d'); ?>">
			                    </div>
			                </div>

			                <div class="fields">
			                    <div class="ten wide required pos_restocking_item_field field">
			                        <label>Item</label>
			                        <div class="ui selection search dropdown" id="pos_restocking_items_drop">
			                            <input type="hidden" name="pos_restocking_items" id="pos_restocking_items">
			                            <div class="default text">Select Item</div>
			                            <div class="menu" id="pos_restocking_menu"></div>
			                        </div>
			                    </div>

			                    <div class="six wide required pos_restocking_quantity_field field">
			                        <label>Quantity</label>
			                        <input type="text" name="pos_restock_quantity" id="pos_restock_quantity" placeholder="Restock Quantity">
			                    </div>
			                </div>
		                	<button class="ui right aligned teal small button" id="pos_restocking_insert" type="submit" form="pos_restocking_form">
			                    <i class="ui plus icon"></i>
			                    Add Stock
			                </button>	
			                <h4>Restocking List</h4>
			                <div class="scrolling content">
			                    <div class="ui celled list" id="pos_restocking_list"></div>
			                </div>

			            </form>
			        </div>

			        <!-- === RESTOCKING RECORDS TAB === -->
			        <div class="ui tab" data-tab="records_tab">
			            <div class="ui form">
			                <div class="fields">
			                    <div class="field">
			                        <label>Report Type</label>
			                        <div class="ui selection dropdown" id="pos_restocking_type_dropdown">
			                            <input type="hidden" name="pos_restocking_report_type" id="pos_restocking_report_type">
			                            <i class="dropdown icon"></i>
			                            <div class="default text">Select Report Type</div>
			                            <div class="menu">
		                                <div class="item" data-value="daily">Daily</div>
		                                <div class="item" data-value="monthly">Monthly</div>
		                                <div class="item" data-value="annual">Annually</div>
			                            </div>
			                        </div>
			                    </div>

			                    <div class="field">
			                        <label>Restock Date</label>
			                        <input type="date" name="pos_restocking_report_date" id="pos_restocking_report_date" placeholder="Restock Date" value="<?= date('Y-m-d'); ?>">
			                    </div>
			                </div>
			            </div>

			            <div class="ui fitted divider"></div>

					    <div class="scrolling content">
					        <!-- Daily Table -->
					        <table class="ui selectable sortable teal table transition hidden pos_restocking_table" id="daily_pos_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="pos_restocking_tbody" id="daily_pos_restocking"></tbody>
					        </table>

					        <!-- Monthly Table -->
					        <table class="ui selectable sortable teal table transition hidden pos_restocking_table" id="monthly_pos_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="pos_restocking_tbody" id="monthly_pos_restocking"></tbody>
					        </table>

					        <!-- Annual Table -->
					        <table class="ui selectable sortable teal table transition hidden pos_restocking_table" id="annual_pos_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="pos_restocking_tbody" id="annual_pos_restocking"></tbody>
					        </table>
					    </div>
			        </div>
			    </div>

			    <!-- Close Icon -->
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui right aligned green invisible small button" id="pos_restocking_submit">
	                    <i class="ui check icon"></i>
                		Confirm
                	</button>
			    </div>
			</div>

		    <div class="ui modal" id="pos_logs_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="pos_logs_header">POS Logs</a>
		        </div>
		        <div class="content">
		        	<div class="ui form">
					    <div class="fields">
					        <!-- Report Type Dropdown -->
					        <div class="field">
					            <label>Log Type</label>
					            <div class="ui selection dropdown" id="pos_log_type_dropdown">
					                <input type="hidden" name="pos_log_type" id="pos_log_type">
					                <i class="dropdown icon"></i>
					                <div class="default text">Select Log Type</div>
					                <div class="menu">
					                    <div class="item" data-value="daily">Daily</div>
					                    <div class="item" data-value="monthly">Monthly</div>
					                    <div class="item" data-value="annual">Annually</div>
					                </div>
					            </div>
					        </div>

					        <!-- log Date Button -->
					        <div class="field">
							    <label>Log Date</label>
							    <input type="date" name="pos_log_date" id="pos_log_date" placeholder="Log Date" value="<?= date('Y-m-d'); ?>">
						  	</div>
					    </div>
					</div>
				</div>
				<div class="ui fitted divider"></div>
		        <div class="scrolling content">
		        	<table class="ui selectable teal sortable fixed table transition hidden pos_log_table" id="pos_daily_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="pos_daily_log">
							
						</tbody>
					</table>
					<table class="ui selectable teal sortable fixed table transition hidden pos_log_table" id="pos_monthly_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="pos_monthly_log">
							
						</tbody>
					</table>
					<table class="ui selectable teal sortable fixed table transition hidden pos_log_table" id="pos_annual_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="pos_annual_log">
							
						</tbody>
					</table>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		        </div>
		    </div>

		    <div class="ui modal" id="pos_transaction_view_modal">
		        <div class="ui header center aligned">
		            <a class="break-text">Transaction View</a>
		        </div>
		        <div class="content">
		        	<h5 class="ui header">
						<div id="pos_transaction_view_activity"></div>
						<div class="sub header" id="pos_transaction_view_date"></div>
					</h5>
		        </div>
		        <div class="scrolling content">
		        	<table class="ui selectable sortable teal table">
			            <thead>
			                <tr>
			                    <th class="sorted descending">No.</th>
			                    <th>Item Name</th>
			                    <th>Item Price</th>
			                    <th>Quantity</th>
			                    <th>Total Price</th>
			                </tr>
			            </thead>
			            <tbody id="pos_transaction_view_container"></tbody>
			            <tfoot>
			                <tr>
			                    <td colspan="4" class="right aligned"><strong>Total:</strong></td>
			                    <td>
			                    	<u id="pos_transaction_view_total"></u>
			                    </td>
			                </tr>
			            </tfoot>
			        </table>
				</div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		            <div class="ui purple pointered small button" id="pos_transaction_void_button" data-pos_transaction_code="" data-pos_transaction_type="">
		                <i class="ui times pointered icon"></i>
		                Void
		            </div>
		        </div>
		    </div>



		    <div class="ui mini modal" id="new_supply_item_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="new_client_header">New Item</a>
			    </div>
			    <div class="scrolling content">
			        <form class="ui padded basic segment form" id="new_supply_item_form">
			            <div class="required field">
			                <label>Item Name</label>
			                <input type="text" name="new_supply_item_name" id="new_supply_item_name" placeholder="e.g. Teddy Bear - Pink">
			            </div>
			            <div class="required field">
			                <label>Item Price</label>
			                <input type="text" name="new_supply_item_price" id="new_supply_item_price" placeholder="₱">
			            </div>
			            <div class="required field">
			                <label>Item Unit (Singular)</label>
			                <input type="text" name="new_supply_item_unit" id="new_supply_item_unit" placeholder="e.g. piece, box">
			            </div>
			            <div class="required field">
			                <label>Initial Stock</label>
			                <input type="text" name="new_supply_item_stock" id="new_supply_item_stock" placeholder="Initial Stock">
			            </div>
			            <div class="required field">
			                <label>Low Stock Level</label>
			                <input type="text" name="new_supply_item_low" id="new_supply_item_low" placeholder="Initial Stock">
			            </div>
			            <div class="required field">
			                <label>Item Image</label>
			                <input class="invisible" type="text" readonly name="new_supply_item_image_name" id="new_supply_item_image_name" value="">
			                
			                <div class="ui segment center aligned">
			                    <video id="new_supply_camera_stream" autoplay></video>
			                    <canvas id="new_supply_captured_canvas" style="display:none;"></canvas>
			                    <img id="new_supply_uploaded_preview" style="aspect-ratio: 5 / 4; display:none; width:100%; height:auto; object-fit:cover;">

			                    <!-- Hidden input for file upload -->
			                    <input type="file" id="new_supply_image_file" accept="image/*" style="display:none;">

			                    <div class="ui mini buttons">
			                        <button type="button" id="new_supply_capture_button" class="ui teal button" style="display:none;">Capture</button>
			                        <button type="button" id="new_supply_retake_button" class="ui yellow button" style="display:none;">Retake</button>
			                    </div>
			                </div>
			            </div>
			            <input type="hidden" id="new_supply_item_image" name="new_supply_item_image">
			        </form>
			    </div>
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui button green small button" form="new_supply_item_form" type="submit">
			            Confirm
			        </button>
			    </div>
			</div>
			<div class="ui mini modal" id="update_supply_item_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="update_client_header">Edit Item</a>
			    </div>
			    <div class="scrolling content">
			        <form class="ui padded basic segment form" id="update_supply_item_form">
					    <!-- Hidden ID for updating -->
					    <input type="hidden" name="update_supply_item_id" id="update_supply_item_id">

					    <div class="required field">
					        <label>Item Name</label>
					        <input type="text" name="update_supply_item_name" id="update_supply_item_name" placeholder="e.g. Teddy Bear - Pink">
					    </div>
					    <div class="required field">
					        <label>Item Price</label>
					        <input type="text" name="update_supply_item_price" id="update_supply_item_price" placeholder="₱">
					    </div>
					    <div class="required field">
					        <label>Item Unit (Singular)</label>
					        <input type="text" name="update_supply_item_unit" id="update_supply_item_unit" placeholder="e.g. piece, box">
					    </div>
					    <div class="required field">
					        <label>Current Stock</label>
					        <input type="text" name="update_supply_item_stock" id="update_supply_item_stock" placeholder="Initial Stock">
					    </div>
					    <div class="required field">
					        <label>Low Stock Level</label>
					        <input type="text" name="update_supply_item_low" id="update_supply_item_low" placeholder="Initial Stock">
					    </div>

					    <!-- Current Image Section -->
					    <div class="field" id="current_supply_image_field" style="text-align:center;">
					        <label>Current Item Image</label>
					        <div class="ui segment center aligned">
					            <img id="current_supply_item_image" 
					                 src="" 
					                 alt="Current Item Image"
					                 style="max-width:100%; aspect-ratio:5/4; object-fit:cover;">
					            <div class="ui labeled green mini icon button" id="update_supply_item_image_button">
					                <i class="redo icon"></i>
					                Retake Image
					            </div>
					        </div>
					    </div>

					    <!-- Webcam Section -->
					    <div class="field invisible" id="update_supply_webcam_field" style="text-align:center;">
					        <label>Replace Item Image (Optional)</label>
					        <input class="invisible" type="text" readonly name="update_supply_item_image_name" id="update_supply_item_image_name" value="">

					        <div class="ui segment center aligned">
					            <video id="update_supply_camera_stream" autoplay></video>
					            <canvas id="update_supply_captured_canvas" style="display:none;"></canvas>
					            <img id="update_supply_uploaded_preview" style="aspect-ratio: 5 / 4; display:none; width:100%; height:auto; object-fit:cover;">
					            
					            <input type="file" id="update_supply_image_file" accept="image/*" style="display:none;">
					            <div class="ui mini buttons">
					                <button type="button" id="update_supply_capture_button" class="ui teal button">Capture</button>
					                <button type="button" id="update_supply_retake_button" class="ui yellow button" style="display:none;">Retake</button>
					                <button type="button" id="cancel_supply_image_update" class="ui orange button">Cancel</button>
					            </div>
					        </div>
					    </div>

					    <input type="hidden" id="update_supply_item_image" name="update_supply_item_image">
					</form>

			    </div>
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui button green small button" form="update_supply_item_form" type="submit">
			            Confirm
			        </button>
			    </div>
			</div>

			<div class="ui tiny modal" id="supply_checkout_cart_modal">
		        <div class="ui header center aligned">
		            <a class="break-text">Supply Checkout</a>
		        </div>
		        <div class="scrolling content">
		        	<div class="ui centered grid transition" id="supply_checkout_cart_empty_message">
			            <div class="sixteen wide column">
			                <h2 class="ui grey header huge center aligned">
			                    Empty
			                </h2>
			            </div>
			        </div>
		        	<div class="ui relaxed divided list" id="supply_checkout_cart_content">
						
					</div>
				</div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		            <button class="ui button green small button" id="supply_checkout_submit">
			            Checkout
			        </button>
		        </div>
		    </div>

		    <div class="ui small modal" id="supply_checkouts_modal">
			    <div class="ui header center aligned">
			        <a class="break-text" id="supply_checkouts_header">Supply Records</a>
			    </div>

			    <div class="content">
			        <div class="ui form">
			            <div class="fields">
			                <div class="field">
			                    <label>Report Type</label>
			                    <div class="ui selection dropdown" id="supply_checkouts_type_dropdown">
			                        <input type="hidden" name="supply_checkouts_type" id="supply_checkouts_type">
			                        <i class="dropdown icon"></i>
			                        <div class="default text">Select Report Type</div>
			                        <div class="menu">
			                            <div class="item" data-value="daily">Daily</div>
			                            <div class="item" data-value="monthly">Monthly</div>
			                            <div class="item" data-value="annual">Annually</div>
			                        </div>
			                    </div>
			                </div>

			                <div class="field">
			                    <label>Date</label>
			                    <input type="date" name="supply_checkouts_date" id="supply_checkouts_date" placeholder="Date" value="<?= date('Y-m-d'); ?>">
			                </div>
			            </div>
			        </div>
			    </div>

			    <div class="ui fitted divider"></div>

			    <div class="scrolling content">
			        <!-- Daily Table -->
			        <table class="ui selectable sortable teal table transition hidden supply_checkouts_table" id="daily_supply_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="supply_checkouts_tbody" id="daily_supply_checkouts"></tbody>
			        </table>

			        <!-- Monthly Table -->
			        <table class="ui selectable sortable teal table transition hidden supply_checkouts_table" id="monthly_supply_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="supply_checkouts_tbody" id="monthly_supply_checkouts"></tbody>
			        </table>

			        <!-- Annual Table -->
			        <table class="ui selectable sortable teal table transition hidden supply_checkouts_table" id="annual_supply_checkouts_table">
			            <thead>
			                <tr>
			                    <th>Reference Code</th>
			                    <th>Total Items</th>
			                    <th class="sorted descending">Activity Date</th>
			                </tr>
			            </thead>
			            <tbody class="supply_checkouts_tbody" id="annual_supply_checkouts"></tbody>
			        </table>
			    </div>

			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			    </div>
			</div>

			<div class="ui small modal" id="supply_restocking_modal">
			    <div class="ui header center aligned">
			        <a class="break-text">Supply Restocking</a>
			    </div>

			    <div class="content">
			        <!-- Tab Menu -->
					<div class="ui secondary labeled icon pointing tabular menu fluid item two">
			            <a class="item active" data-tab="form_tab" id="restock_items">Restock Items</a>
			            <a class="item" data-tab="records_tab" id="records_tab">Restocking Records</a>
			        </div>

			        <!-- === RESTOCKING FORM TAB === -->
			        <div class="ui active tab" data-tab="form_tab">
			            <form class="ui tiny form" id="supply_restocking_form">
			                <div class="fields">
			                    <div class="six wide required supply_restocking_date_field field">
			                        <label>Restocking Date</label>
			                        <input type="date" name="supply_restocking_date" id="supply_restocking_date" required placeholder="Restocking Date" value="<?= date('Y-m-d'); ?>">
			                    </div>
			                </div>

			                <div class="fields">
			                    <div class="ten wide required supply_restocking_item_field field">
			                        <label>Item</label>
			                        <div class="ui selection search dropdown" id="supply_restocking_items_drop">
			                            <input type="hidden" name="supply_restocking_items" id="supply_restocking_items">
			                            <div class="default text">Select Item</div>
			                            <div class="menu" id="supply_restocking_menu"></div>
			                        </div>
			                    </div>

			                    <div class="six wide required supply_restocking_quantity_field field">
			                        <label>Quantity</label>
			                        <input type="text" name="supply_restock_quantity" id="supply_restock_quantity" placeholder="Restock Quantity">
			                    </div>
			                </div>
		                	<button class="ui right aligned teal small button" id="supply_restocking_insert" type="submit" form="supply_restocking_form">
			                    <i class="ui plus icon"></i>
			                    Add Stock
			                </button>	
			                <h4>Restocking List</h4>
			                <div class="scrolling content">
			                    <div class="ui celled list" id="supply_restocking_list"></div>
			                </div>

			            </form>
			        </div>

			        <!-- === RESTOCKING RECORDS TAB === -->
			        <div class="ui tab" data-tab="records_tab">
			            <div class="ui form">
			                <div class="fields">
			                    <div class="field">
			                        <label>Report Type</label>
			                        <div class="ui selection dropdown" id="supply_restocking_type_dropdown">
			                            <input type="hidden" name="supply_restocking_report_type" id="supply_restocking_report_type">
			                            <i class="dropdown icon"></i>
			                            <div class="default text">Select Report Type</div>
			                            <div class="menu">
		                                <div class="item" data-value="daily">Daily</div>
		                                <div class="item" data-value="monthly">Monthly</div>
		                                <div class="item" data-value="annual">Annually</div>
			                            </div>
			                        </div>
			                    </div>

			                    <div class="field">
			                        <label>Restock Date</label>
			                        <input type="date" name="supply_restocking_report_date" id="supply_restocking_report_date" placeholder="Restock Date" value="<?= date('Y-m-d'); ?>">
			                    </div>
			                </div>
			            </div>

			            <div class="ui fitted divider"></div>

					    <div class="scrolling content">
					        <!-- Daily Table -->
					        <table class="ui selectable sortable teal table transition hidden supply_restocking_table" id="daily_supply_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="supply_restocking_tbody" id="daily_supply_restocking"></tbody>
					        </table>

					        <!-- Monthly Table -->
					        <table class="ui selectable sortable teal table transition hidden supply_restocking_table" id="monthly_supply_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="supply_restocking_tbody" id="monthly_supply_restocking"></tbody>
					        </table>

					        <!-- Annual Table -->
					        <table class="ui selectable sortable teal table transition hidden supply_restocking_table" id="annual_supply_restocking_table">
					            <thead>
					                <tr>
					                    <th>Reference Code</th>
					                    <th>Total Items</th>
					                    <th class="sorted descending">Activity Date</th>
					                    <th>Timestamp</th>
					                </tr>
					            </thead>
					            <tbody class="supply_restocking_tbody" id="annual_supply_restocking"></tbody>
					        </table>
					    </div>
			        </div>
			    </div>

			    <!-- Close Icon -->
			    <div class="actions modal-actions">
			        <div class="ui orange right corner deny pointered small label">
			            <i class="ui times pointered big icon"></i>
			        </div>
			        <button class="ui right aligned green invisible small button" id="supply_restocking_submit">
	                    <i class="ui check icon"></i>
                		Confirm
                	</button>
			    </div>
			</div>

		    <div class="ui modal" id="supply_logs_modal">
		        <div class="ui header center aligned">
		            <a class="break-text" id="supply_logs_header">Supply Logs</a>
		        </div>
		        <div class="content">
		        	<div class="ui form">
					    <div class="fields">
					        <!-- Report Type Dropdown -->
					        <div class="field">
					            <label>Log Type</label>
					            <div class="ui selection dropdown" id="supply_log_type_dropdown">
					                <input type="hidden" name="supply_log_type" id="supply_log_type">
					                <i class="dropdown icon"></i>
					                <div class="default text">Select Log Type</div>
					                <div class="menu">
					                    <div class="item" data-value="daily">Daily</div>
					                    <div class="item" data-value="monthly">Monthly</div>
					                    <div class="item" data-value="annual">Annually</div>
					                </div>
					            </div>
					        </div>

					        <!-- log Date Button -->
					        <div class="field">
							    <label>Log Date</label>
							    <input type="date" name="supply_log_date" id="supply_log_date" placeholder="Log Date" value="<?= date('Y-m-d'); ?>">
						  	</div>
					    </div>
					</div>
				</div>
				<div class="ui fitted divider"></div>
		        <div class="scrolling content">
		        	<table class="ui selectable teal sortable fixed table transition hidden supply_log_table" id="supply_daily_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="supply_daily_log">
							
						</tbody>
					</table>
					<table class="ui selectable teal sortable fixed table transition hidden supply_log_table" id="supply_monthly_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="supply_monthly_log">
							
						</tbody>
					</table>
					<table class="ui selectable teal sortable fixed table transition hidden supply_log_table" id="supply_annual_log_table">
						<thead>
							<tr>
								<th class="three wide">Activity Type</th>
								<th class="four wide">Reference Code</th>
								<th class="six wide">Activity/Items</th>
								<th class="sorted ascending three wide">Timestamp</th>
							</tr>
						</thead>
						<tbody id="supply_annual_log">
							
						</tbody>
					</table>
		        </div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		        </div>
		    </div>

		    <div class="ui modal" id="supply_transaction_view_modal">
		        <div class="ui header center aligned">
		            <a class="break-text">Transaction View</a>
		        </div>
		        <div class="content">
		        	<h5 class="ui header">
						<div id="supply_transaction_view_activity"></div>
						<div class="sub header" id="supply_transaction_view_date"></div>
					</h5>
		        </div>
		        <div class="scrolling content">
		        	<table class="ui selectable sortable teal table">
			            <thead>
			                <tr>
			                    <th class="sorted descending">No.</th>
			                    <th>Item Name</th>
			                    <th>Item Price</th>
			                    <th>Quantity</th>
			                    <th>Total Price</th>
			                </tr>
			            </thead>
			            <tbody id="supply_transaction_view_container"></tbody>
			            <tfoot>
			                <tr>
			                    <td colspan="4" class="right aligned"><strong>Total:</strong></td>
			                    <td>
			                    	<u id="supply_transaction_view_total"></u>
			                    </td>
			                </tr>
			            </tfoot>
			        </table>
				</div>
		        <div class="actions modal-actions">
		            <div class="ui orange right corner deny pointered small label">
		                <i class="ui times pointered big icon"></i>
		            </div>
		            <div class="ui purple pointered small button" id="supply_transaction_void_button" data-supply_transaction_code="" data-supply_transaction_type="">
		                <i class="ui times pointered icon"></i>
		                Void
		            </div>
		        </div>
		    </div>

















 
		</main>
		<footer>
			<?php include 'esses/footer.php';?>
		</footer>
	</body>
</div>
<script type="text/javascript">
    $('.menu .item').tab();

    function monitor_item_stock(on_change_callback) {
	    function attach_observer(item) {
	        const observer = new MutationObserver(function(mutations) {
	            mutations.forEach(function() {
	                on_change_callback(item);
	            });
	        });

	        observer.observe(item, {
	            childList: true,  // watches added/removed text nodes
	            characterData: true, // watches changes to text nodes
	            subtree: true       // watches inside the element
	        });
	    }

	    // Attach to existing items
	    document.querySelectorAll('.item_stock').forEach(attach_observer);

	    // Watch for newly added items
	    const dom_observer = new MutationObserver(function(mutations) {
	        mutations.forEach(function(mutation) {
	            mutation.addedNodes.forEach(function(node) {
	                if (node.nodeType === 1) {
	                    if (node.classList.contains('item_stock')) attach_observer(node);
	                    node.querySelectorAll('.item_stock').forEach(attach_observer);
	                }
	            });
	        });
	    });

	    dom_observer.observe(document.body, { childList: true, subtree: true });
	}


	function check_active_tab() {
	    const tabs = $('#time_manager_tab, #pos_tab, #supply_tab');
	    const tab_functions = {
	        time_manager_tab: load_time_manager_tab,
	        pos_tab: load_pos_tab,
	        supply_tab: load_supply_tab
	    };

	    const tab_check = (tab_id) => {
	        if ($(`#${tab_id}`).hasClass('active')) {
	            if (tab_functions[tab_id]) {
	                tab_functions[tab_id](); // Call the correct function
	            }
	            return tab_id;
	        }
	    };

	    tabs.each(function() {
	        if ($(this).hasClass('active')) {
	            tab_check(this.id);
	        }
	    });

	    tabs.on('click', function() {
            tab_check(this.id)
	    });
	}

	function load_time_manager_tab() {
		$('.floater-button').addClass('invisible');
		$('#time_manager_tab_actions').removeClass('invisible');
		load_active_clients();
	}

	function load_pos_tab() {
		$('.floater-button').addClass('invisible');
		$('#pos_tab_actions').removeClass('invisible');
		load_pos_inventory();
	}

	function load_supply_tab() {
		$('.floater-button').addClass('invisible');
		$('#supply_tab_actions').removeClass('invisible');
		load_supply_inventory();
	}

	$('#pos_checkout_cart_activator').on('click', function() {
		$('#pos_checkout_cart_modal')
		.modal({
	        useFlex: true,
	        allowMultiple: false,
	        autofocus: false,
	        blurring: true,
	        closable: false,
	        onHide: function() {
	            // stop_update_pos_camera();
	        },
	        onShow: function(){
	        	// start_update_pos_camera();
	        }
	    })
	    .modal('show')

	})
	
	$('#pos_checkouts_type_dropdown').dropdown('set selected', 'daily');

	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0];
	    document.getElementById('pos_checkouts_date').value = today;
	});

	$('#pos_checkouts_activator').on('click', function() {
		$('#pos_checkouts_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_pos_checkout_codes();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});
	$('#pos_restocking_activator').on('click', function() {
		$('#pos_restocking_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	// load_pos_checkouts();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

	function load_pos_checkout_codes() {
	    let pos_checkouts_type = $('#pos_checkouts_type').val();
	    let pos_checkouts_date = $('#pos_checkouts_date').val();

	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/load_pos_checkout_codes',
	        data  : { 
	            pos_checkouts_type: pos_checkouts_type, 
	            pos_checkouts_date: pos_checkouts_date
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response_data = JSON.parse(jqxhr.responseText);

	        $('.pos_checkouts_table').addClass('hidden');
	        $(`#${pos_checkouts_type}_pos_checkouts_table`).removeClass('hidden');

	        $('.pos_checkouts_tbody').html('');

	        if (response_data != '') {

	            $.each(response_data, function(key, value) {
	                var pos_checkout_code = value.pos_checkout_code;
	                var total_item_count  = value.total_item_count;
	                var pos_checkout_date = value.pos_checkout_date;

	                let pos_report = `
	                    <tr class="pointered pos_checkout_view" 
	                        data-pos_checkout_code="${pos_checkout_code}" data-pos_checkout_date="${pos_checkout_date}">
	                        
	                        <td class="no-break">${pos_checkout_code}</td>
	                        <td class="no-break">${total_item_count}</td>
	                        <td class="no-break">${pos_checkout_date}</td>
	                    </tr>
	                `;

	                $(`#${pos_checkouts_type}_pos_checkouts`).append(pos_report);
	            });

	            $('.pos_checkout_view').on('click', function() {
	                let pos_checkout_code = $(this).data('pos_checkout_code');
	                let pos_checkout_date = $(this).data('pos_checkout_date');

	                $('#pos_transaction_view_modal')
	                    .modal({
	                        useFlex: true,
	                        allowMultiple: true,
	                        autofocus: false,
	                        blurring: true,
	                        closable: false,
	                        onShow: function() {
	                            load_pos_checkout(pos_checkout_code, pos_checkout_date);
	                        }
	                    })
	                    .modal('show');
	            });
	        }
	        else {
	            $(`#${pos_checkouts_type}_pos_checkouts_table`).removeClass('hidden');
	            $(`#${pos_checkouts_type}_pos_checkouts`).html(
	                '<tr><td colspan="6" class="center aligned">No records found</td></tr>'
	            );
	        }
	    });
	}
	function load_pos_checkout(pos_checkout_code, pos_checkout_date) {
	    let view_activity = `Checkout: <u>${pos_checkout_code}</u>`;
	    let transaction_date = `Date: <u>${pos_checkout_date}</u>`;
	    $('#pos_transaction_void_button').data('pos_transaction_code', pos_checkout_code);
	    $('#pos_transaction_void_button').data('pos_transaction_type', 'checkout');

	    $('#pos_transaction_view_activity').html(view_activity);
	    $('#pos_transaction_view_date').html(transaction_date);

	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/load_pos_checkout',
	        data  : { 
	            pos_checkout_code: pos_checkout_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response_data = JSON.parse(jqxhr.responseText);

	        if (response_data != '') {

	            $('#pos_transaction_view_container').html('');
	            let final_total = 0;

	            $.each(response_data, function(key, value) {
	                var pos_checkout_id  = value.pos_checkout_id;
	                var pos_item_name  = value.pos_item_name;
	                var pos_item_image = value.pos_item_image;
	                var pos_item_price = value.pos_item_price;
	                var pos_item_count = value.pos_item_count;
	                var pos_item_unit  = value.pos_item_unit;

	                var total_price = pos_item_count * pos_item_price;
	                var display_total_price = parseFloat(total_price).toFixed(2);

	                let formatted_item_price  = pos_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	                let formatted_total_price = display_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	                final_total += total_price;

	                // pluralize units
	                if (pos_item_count > 1) {
	                    unit_last = pos_item_unit[pos_item_unit.length - 1].toLowerCase();

	                    if (
	                        unit_last == 's' ||
	                        unit_last == 'h' && pos_item_unit.endsWith('sh') ||
	                        unit_last == 'h' && pos_item_unit.endsWith('ch') ||
	                        unit_last == 'x' ||
	                        unit_last == 'z'
	                    ) {
	                        pos_item_unit = pos_item_unit + 'es';
	                    } else {
	                        pos_item_unit = pos_item_unit + 's';
	                    }
	                }

	                let view_data = `
						<tr id="pos_checkout_item${pos_checkout_id}" data-pos_checkout_code="${pos_checkout_code}" data-pos_checkout_date="${pos_checkout_date}">
	                        <td>${key+1}</td>
	                        <td class="break-text">
	                			<i class="red pointered x icon  void_pos_checkout_item" data-pos_checkout_id="${pos_checkout_id}" data-pos_item_name="${pos_item_name}"></i>
	                            <img src="<?php echo base_url();?>photos/pos_images/${pos_item_image}" class="ui avatar image">
	                            <span>${pos_item_name}</span>
	                        </td>
	                        <td class="no-break">₱${formatted_item_price}</td>
	                        <td class="no-break">${pos_item_count} ${pos_item_unit}</td>
	                        <td class="no-break">₱${formatted_total_price}</td>
	                    </tr>
	                `;

	                $('#pos_transaction_view_container').append(view_data);
	            });

	            $('.void_pos_checkout_item').on('dblclick', function() {
	            	let pos_checkout_id = $(this).data('pos_checkout_id');
	            	let pos_item_name = $(this).data('pos_item_name');
	            	let confirmed = confirm('Are you sure you want to void this checkout item?');
	            	if (confirmed) {
	            		void_pos_checkout_item(pos_checkout_id, pos_item_name);
	            	}
	            });
	            let formatted_final_total = parseFloat(final_total).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#pos_transaction_view_total').html('₱'+formatted_final_total);

	        } else {

	            $('#pos_transaction_view_container').html('');

	        }
	    });
	}

	function void_pos_checkout_item(pos_checkout_id, pos_item_name) {
	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_pos_checkout_item',
	        data  : { 
	            pos_checkout_id: pos_checkout_id
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
        	if (response == 'success') {
	        	alert(`${pos_item_name} was successfully voided from this checkout.`);
	        	let pos_checkout_code = $(`#pos_checkout_item${pos_checkout_id}`).data('pos_checkout_code');
	        	let pos_checkout_date = $(`#pos_checkout_item${pos_checkout_id}`).data('pos_checkout_date');

	        	load_pos_checkout(pos_checkout_code, pos_checkout_date);
            	load_pos_inventory();
	        }
	        else if (response == 'success-null') {
	        	$('#pos_transaction_view_modal').modal('hide');
	        	alert(`${pos_item_name} was successfully voided. All items from this checkout has been voided.`);
	        	load_pos_checkout_codes();
            	load_pos_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

	function void_pos_checkout(pos_checkout_code) {
		var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_pos_checkout',
	        data  : { 
	            pos_checkout_code: pos_checkout_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	$('#pos_transaction_view_modal').modal('hide');
	        	alert(`Checkout with code "${pos_checkout_code}" was successfully voided.`);
	        	load_pos_checkout_codes();
            	load_pos_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}


	// Auto load on page open
	$('#pos_checkouts_modal').modal({
	    onShow: function() {
	        load_pos_checkout_codes();
	    }
	});

	// Reload when filters change
	$('#pos_checkouts_type_dropdown').dropdown({
	    onChange: function () {
	        load_pos_checkout_codes();
	    }
	});

	$('#pos_checkouts_date').on('change', function () {
	    load_pos_checkout_codes();
	});


	let pos_cart_array = [];
	function load_pos_inventory() {
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_pos_inventory'
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
			    var search_content = [];
        		$(`#pos_items_container`).html('');
                $('#pos_empty_message').addClass('invisible');
                $.each(response_data, function(key, value) {
                    let pos_item_id = value.pos_item_id;
                    let pos_item_name = value.pos_item_name;
                    let pos_item_price = value.pos_item_price;
                    let pos_item_image = value.pos_item_image;
                    let pos_item_stock = value.pos_item_stock;
                    let pos_item_unit = value.pos_item_unit;
                    let pos_item_low = value.pos_item_low;
                    let pos_item_status = value.pos_item_status;


                    let default_pos_item_unit = pos_item_unit;

                    let formatted_item_price = pos_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");


                    if (pos_item_stock > 1) {
						unit_last = pos_item_unit[pos_item_unit.length-1].toLowerCase();
				        if (
					        unit_last == 's' ||
					        unit_last == 'h' && pos_item_unit.endsWith('sh') ||
					        unit_last == 'h' && pos_item_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        pos_item_unit = pos_item_unit + 'es';
					    } else {
					        pos_item_unit = pos_item_unit + 's';
					    }
					}

                    let pos_item = `
						<div class="ui fluid link card transition item_card" id="pos_item${pos_item_id}" data-pos_item_id="${pos_item_id}" data-pos_item_name="${pos_item_name}" data-pos_item_price="${pos_item_price}" data-pos_item_image="${pos_item_image}" data-pos_item_stock="${pos_item_stock}" data-pos_item_unit="${default_pos_item_unit}" data-pos_item_low="${pos_item_low}" data-pos_item_status="${pos_item_status}">
						    <div class="blurring dimmable image image-container">
								<div class="ui dimmer">
									<div class="content">
                    					<div class="ui blue mini inverted button edit_pos_item">
											Edit
										</div>
                    					<br><br>
                    					<div class="ui orange mini inverted button archive_pos_item invisible">
											Archive
										</div>
									</div>
								</div>
                            	<img src="<?php echo base_url();?>photos/pos_images/${pos_item_image}">
							</div>
						    <div class="content">
					    		<input type="text" class="pos_card_focus_handler file_input" data-item_id="${pos_item_id}">
						    	<div class="item">
									<h5 class="no-break pos_item_name" data-content="${pos_item_name}" data-position="bottom left">${pos_item_name}</h5>
								    <div class="content">
										<a class="ui tiny grey header"><x class="item_stock pos_item_stock" data-item_low="${pos_item_low}" data-item_id="${pos_item_id}">${pos_item_stock}</x> <x class="pos_item_unit">${pos_item_unit}</x></a>
										<a class="ui yellow right floated tag label small">₱ ${formatted_item_price}</a>
								    </div>
								</div>
						    </div>
						    <div class="extra content">
						    	<div class="ui left floated small buttons">
									<button class="ui basic button pos_count_minus">
										<i class="left floated minus icon"></i>
									</button>
									<div class="or pos_count_container" data-text="0" data-max_limit="${pos_item_stock}"></div>
									<button class="ui basic button pos_count_plus">
										<i class="right floated plus icon"></i>
									</button>
								</div>
								<div class="ui right floated basic small button add_to_pos_cart invisible">
									Add
									<i class="right cart plus icon"></i>
								</div>
						    </div>
						</div>
                    `;

                    let pos_restocking_item = `
                    	<div class="item pos_restocking_item" data-value="${pos_item_id}" data-pos_item_name="${pos_item_name}" data-pos_item_image="${pos_item_image}">
							<img class="ui mini item_avatar image" src="<?php echo base_url();?>photos/pos_images/${pos_item_image}">
							<span>${pos_item_name}</span>
						</div>
                    `;

            		$(`#pos_items_container`).append(pos_item);
            		$(`#pos_restocking_menu`).append(pos_restocking_item);
		      		search_content.push({id:pos_item_id,title:pos_item_name,description:"₱ "+formatted_item_price});
                })
				$('.pos_item_name').popup({
                	on: 'click'
                });

				let pos_restocking_array = [];
				let pos_restocking_list_item = '';
				let pos_list_restock_date = '';
				let pos_list_item_id = '';
				let pos_list_item_name = '';
				let pos_list_item_image = '';

				$('#pos_restocking_items_drop').dropdown({
				    action: function (text, value, element) {
				    	$('#pos_restocking_menu .pos_restocking_item').removeClass('invisible');
						$('#pos_restocking_menu .pos_restocking_item').addClass('item');

				        pos_restocking_array.forEach(function(item) {
			                let element = $(`#pos_restocking_items_drop [data-value='${item.pos_item_id}']`);

			                element.addClass('invisible');
			                element.removeClass('active');
							element.removeClass('item');
						});
						$(this).dropdown('set selected', value);
						$(this).dropdown('hide');
						pos_list_item_id = value;
						pos_list_item_name = $(element).data('pos_item_name');
						pos_list_item_image = $(element).data('pos_item_image');
						$('#pos_restock_quantity').trigger('focus');
				    },
				    forceSelection: false
				});
				// $('#pos_restocking_insert').on('click', function(){
					
				// });

				$('#pos_restocking_form')
				    .form({
				        on: 'change',
				        inline: false,
				        transition: 'fade',
				        onSuccess: function(event) {
				            event.preventDefault();

				            let pos_list_restock_date = $('#pos_restocking_date').val();
							let pos_list_restock_quantity = $('#pos_restock_quantity').val();
							let errors = [];

							if (!pos_list_restock_date) {
								errors.push("Restocking Date");
							}
							if (!pos_list_item_id) {
								errors.push("Item");
							}
							if (!pos_list_restock_quantity) {
								errors.push("Quantity");
							}

							if (errors.length > 0) {
								if (errors.length === 1) {
									alert(`Please provide a valid ${errors[0]}.`);
								} else if (errors.length === 2) {
									alert(`Please provide valid ${errors[0]} and ${errors[1]}.`);
								} else {
									alert(`Please provide valid ${errors.join(", ")}.`);
								}
								return; // Stop execution if there are errors
							}

							if (pos_list_restock_quantity <= 0) {
								alert("Please provide a valid quantity.")
							}
							else {
								pos_restocking_list_item = `
									<div class="item" data-pos_item_id="${pos_list_item_id}">
										<div class="right floated content">
						                	<i class="ui x red icon pointered pos_restocking_item_remover"></i>
										</div>
			                        	<img class="ui mini item_avatar image" src="<?php echo base_url();?>photos/pos_images/${pos_list_item_image}">
								    	<div class="content">
									      	<a class="header">${pos_list_item_name}</a>
									    	<div class="description">
												x${pos_list_restock_quantity}
									    	</div>
									    </div>
									</div>
								`;
								$('#pos_restocking_list').append(pos_restocking_list_item);	

								$('.pos_restocking_item_remover').on('dblclick', function() {
									confirmed = confirm('Remove this restocking item?');
									if (confirmed) {
									    const item = $(this).closest('.item');
									    const pos_item_id = item.data('pos_item_id');
									    $(`#pos_restocking_list .item[data-pos_item_id='${pos_item_id}']`).remove();

									    for (let i = 0; i < pos_restocking_array.length; i++) {
								            if (pos_restocking_array[i].pos_item_id == pos_item_id) { // use == to allow type coercion
								                pos_restocking_array.splice(i, 1);
								                break;
								            }
								        }

										$('#pos_restocking_menu .pos_restocking_item').removeClass('invisible');
										$('#pos_restocking_menu .pos_restocking_item').addClass('item');
									    pos_restocking_array.forEach(function(item) {
							                let element = $(`#pos_restocking_items_drop [data-value='${item.pos_item_id}']`);

							                element.addClass('invisible');
							                element.removeClass('active');
											element.removeClass('item');
										});

										if ($('#pos_restocking_menu .item').length === $('#pos_restocking_menu .item.invisible').length) {
										    $('#pos_restocking_items_drop').addClass('disabled');
										} 
										else {
										    $('#pos_restocking_items_drop').removeClass('disabled');
										}
									}
								});

								pos_restocking_array.push({
								    pos_item_id: pos_list_item_id,
								    pos_item_count: pos_list_restock_quantity
								});
								$('#pos_restocking_menu .pos_restocking_item').removeClass('invisible');
								$('#pos_restocking_menu .pos_restocking_item').addClass('item');
								pos_restocking_array.forEach(function(item) {
									// alert(item.pos_item_id)
					                let element = $(`#pos_restocking_items_drop [data-value='${item.pos_item_id}']`);

					                element.addClass('invisible');
					                element.removeClass('active');
									element.removeClass('item');
								});

								if ($('#pos_restocking_menu .item').length === $('#pos_restocking_menu .item.invisible').length) {
								    $('#pos_restocking_items_drop').addClass('disabled');
								} 
								else {
								    $('#pos_restocking_items_drop').removeClass('disabled');
								}

								// $('#pos_restocking_form').form('reset');
								$('#pos_restocking_items_drop').dropdown('clear');
								$('#pos_restock_quantity').val('');

								$('.pos_restocking_divider').removeClass('invisible');
							}
							if (pos_restocking_array.length > 0) {
								$('#pos_restocking_submit').removeClass('invisible');
							}

	        				$('#pos_restocking_items_drop .search').focus();
				        },
				        fields: {
				            pos_restocking_date: {
				                identifier: 'pos_restocking_date',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            },
				            pos_restocking_items_drop: {
				                identifier: 'pos_restocking_items_drop',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            },
				            pos_restock_quantity: {
				                identifier: 'pos_restock_quantity',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            }
				        }
				    })
				;

				$('#pos_restocking_submit').on('dblclick', function(e) {
				    e.preventDefault();

				    if (!pos_restocking_array || pos_restocking_array.length === 0) {
				        alert('Cart is empty.');
				        return;
				    }

				    let formData = new FormData();
				    formData.append('pos_restocking_items', JSON.stringify(pos_restocking_array)); // send cart as JSON string
					formData.append('pos_restocking_date', $('#pos_restocking_date').val());
				    
				    $.ajax({
				        url: '<?php echo base_url(); ?>i.php/sys_control/pos_restock',
				        method: 'POST',
				        data: formData,
				        processData: false,  // important for FormData
				        contentType: false,  // important for FormData
				        success: function (response) {
				            if (response === 'success') {
				                pos_restocking_array = []; // clear current cart
						    	$('#pos_checkout_cart_empty_message').addClass('hidden');
						    	$('#pos_checkout_cart_empty_message').removeClass('visible');
						    	$('#pos_checkout_cart_content').html('');
				                alert('Restocking successful! Inventory content will reload shortly...');
				                load_pos_inventory();
				                $('#pos_restocking_modal').modal('hide')
				            } 
				            else if (response === 'empty_cart') {
				                alert('Restocking is empty. Nothing to insert.');
				            }
				            else {
				                alert('Restocking failed. Try again.');
				            }
				        },
				        error: function (xhr, status, error) {
				            console.error(xhr.responseText);
				            alert('AJAX error during checkout.');
				        }
				    });
				});

                $('#pos_inventory_search')
					.search({
						source: search_content,
						fullTextSearch: 'exact',
						maxResults: 100,
						minCharacters: 2,
						showNoResults: false,
						onSelect: function(result, response) {
							search_pos_item_id = result.id;
							$('.pos_card_focus_handler[data-item_id="' + result.id + '"]').focus();

							$('#pos_item'+search_pos_item_id)
								.transition('pulse')
								.transition('flash')
							;
						}
					})
				;

                function open_pos_item_update_modal(data) {
			        $('#update_pos_item_id').val(data.pos_item_id);
			        $('#update_pos_item_name').val(data.pos_item_name);
			        $('#update_pos_item_price').val(data.pos_item_price);
			        $('#update_pos_item_stock').val(data.pos_item_stock);
			        $('#update_pos_item_unit').val(data.pos_item_unit);
			        $('#update_pos_item_low').val(data.pos_item_low);
			        $('#current_pos_item_image').attr('src', data.pos_item_image_url);

			        $('#update_pos_item_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: false,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onHide: function() {
					            // stop_update_pos_camera();
					        },
					        onShow: function(){
					        	// start_update_pos_camera();
					        }
			            })
			            .modal('show')
			        ;
			    }
                $('.edit_pos_item').on('click', function() {
					let card = $(this).closest('.card'); // go up to the parent card

					let pos_item_id = card.data('pos_item_id');
					let pos_item_name = card.data('pos_item_name');
					let pos_item_price = card.data('pos_item_price');
					let pos_item_image = card.data('pos_item_image');
					let pos_item_stock = card.data('pos_item_stock');
					let pos_item_unit = card.data('pos_item_unit');
					let pos_item_low = card.data('pos_item_low');
					let pos_item_status = card.data('pos_item_status');

			        open_pos_item_update_modal({
				        pos_item_id: pos_item_id,
				        pos_item_name: pos_item_name,
				        pos_item_price: pos_item_price,
				        pos_item_image: pos_item_image,
				        pos_item_stock: pos_item_stock,
				        pos_item_unit: pos_item_unit,
				        pos_item_low: pos_item_low,
				        pos_item_status: pos_item_status,
				        pos_item_image_url: `<?php echo base_url();?>photos/pos_images/${pos_item_image}`
				    });
        		});
        		$('.pos_count_minus, .pos_count_plus').on('click', function() {
					let container = $(this).siblings('.pos_count_container');
					let item_count = Number(container.attr('data-text'));
					let max_limit = Number(container.attr('data-max_limit'));
					// let max_limit = pos_item_stock;
					let card = $(this).closest('.card');
					let stock_el = card.find('.pos_item_stock').first();
					let current_stock = Number(stock_el.text());

					let unit_el = card.find('.pos_item_unit').first();
					let current_unit = unit_el.text();
					let add_button = card.find('.add_to_pos_cart');

					if ($(this).hasClass('pos_count_plus')) {
						if (item_count < max_limit && current_stock > 0) {
							item_count++;
							current_stock--;
						}
					} else {
						if (item_count > 0) {
							item_count--;
							current_stock++;
						}
					}

					container.attr('data-text', item_count);
					stock_el.text(current_stock);
					item_count > 0 ? add_button.removeClass('invisible') : add_button.addClass('invisible');

					if (current_unit.endsWith('es')) {
					    if (/(sh|ch|x|z|s)es$/.test(current_unit)) {
					        current_unit = current_unit.slice(0, -2);
					    } else if (!current_unit.endsWith('ses')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					} else if (current_unit.endsWith('s')) {
					    if (!current_unit.endsWith('ss')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					}

					if (current_stock != 1) {
					    unit_last = current_unit[current_unit.length - 1].toLowerCase();
					    if (
					        unit_last == 's' ||
					        unit_last == 'h' && current_unit.endsWith('sh') ||
					        unit_last == 'h' && current_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        current_unit = current_unit + 'es';
					    } 
					    else {
					        current_unit = current_unit + 's';
					    }
					} 
					else {
					    if (current_unit.endsWith('es')) {
					        current_unit = current_unit.slice(0, -2);
					    } 
					    else if (current_unit.endsWith('s')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					}

					unit_el.text(current_unit);
				});

				$('.add_to_pos_cart').on('click', function() {
				    let card = $(this).closest('.card');
				    let add_button = card.find('.add_to_pos_cart');
				    let pos_checkout_cart_el = $('#pos_checkout_cart_activator');

				    let item_count_el = card.find('.pos_count_container').first();
				    let item_stock_el = card.find('.pos_item_stock').first();

				    let pos_item_id     = card.data('pos_item_id');
				    let pos_item_name   = card.data('pos_item_name');
				    let pos_item_price  = card.data('pos_item_price');
				    let pos_item_image  = card.data('pos_item_image');
				    let pos_item_stock  = card.data('pos_item_stock');
				    let pos_item_unit   = card.data('pos_item_unit');
				    let pos_item_low    = card.data('pos_item_low');
				    let pos_item_status = card.data('pos_item_status');

				    let item_stock = item_stock_el.text();
				    let item_count = item_count_el.attr('data-text');
				    let remaining = item_stock;
				    let total_item_price = pos_item_price * item_count;

				    let formatted_item_price = parseFloat(total_item_price).toFixed(2);
				    formatted_item_price = formatted_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				    item_stock_el.text(remaining);
				    item_count_el.attr('data-text', 0);
				    add_button.addClass('invisible');

				    card.transition({
				        animation: 'bounce',
				        onComplete: function() {
				            pos_checkout_cart_el.transition('jiggle')
				        }
				    });

				    if (item_count > 1) {
				        unit_last = pos_item_unit[pos_item_unit.length-1].toLowerCase();
				        if (
				            unit_last == 's' ||
				            unit_last == 'h' && pos_item_unit.endsWith('sh') ||
				            unit_last == 'h' && pos_item_unit.endsWith('ch') ||
				            unit_last == 'x' ||
				            unit_last == 'z'
				        ) {
				            pos_item_unit = pos_item_unit + 'es';
				        } else {
				            pos_item_unit = pos_item_unit + 's';
				        }
				    }

				    pos_cart_array.push({
					    pos_item_id: pos_item_id,
					    pos_item_name: pos_item_name,
					    pos_item_price: pos_item_price,
					    item_count: item_count,
					    pos_item_unit: pos_item_unit,
					    pos_item_image: pos_item_image,
					    total_item_price: total_item_price
					});

				    item_data = `
				        <div class="item pos_checkout_cart_item" data-pos_item_id="${pos_item_id}">
				            <img class="ui item_avatar image" src="<?php echo base_url();?>photos/pos_images/${pos_item_image}">
				            <div class="content">
				                <div class="header pos_cart_item_name">${pos_item_name}</div>
				                <div class="meta">
				                    <span>₱<x class="pos_cart_item_price">${formatted_item_price}</x>
				                    - <x class="pos_cart_item_count">${item_count}</x>
				                    <x class="pos_cart_item_unit">${pos_item_unit}</x></span>
				                </div>
				            </div>
				            <div class="right floated content pos_cart_actions">
				                <i class="ui link red minus circle icon pos_remove_item"></i>
				            </div>
				        </div>`;

				    $('#pos_checkout_cart_content').append(item_data);

				    let cart_item_count = $('#pos_checkout_cart_content .pos_checkout_cart_item').length;

				    if (cart_item_count > 0) {
				    	$('#pos_checkout_code').addClass('hidden');
				    	$('#pos_checkout_code').removeClass('visible');
				    	
				    	$('#pos_checkout_cart_empty_message').addClass('hidden');
				    	$('#pos_checkout_cart_empty_message').removeClass('visible');
				    }
				    else {
				    	$('#pos_checkout_cart_empty_message').addClass('visible');
				    	$('#pos_checkout_cart_empty_message').removeClass('hidden');
				    }
				});


                $('.special.cards .image').dimmer({
				  	on: 'hover'
				});
            }
            else {
        		$(`#pos_items_container`).html('');
                $('#pos_empty_message').removeClass('invisible');
            }
        })
    }

    $('#pos_log_type_dropdown').dropdown('set selected', 'daily');
	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
	    const date_input = document.getElementById('pos_log_date');
	    date_input.value = today;
	});

	$('#pos_log_type_dropdown,#pos_log_date').on('change', function() {
    	load_pos_logs();
	});

    function load_pos_logs() {
		let pos_log_type = $('#pos_log_type').val();
		let pos_log_date = $('#pos_log_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_pos_logs',
            data  : { 
            	pos_log_type:pos_log_type, 
            	pos_log_date:pos_log_date
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.pos_log_table`).addClass('hidden');
    		$(`#pos_${pos_log_type}_log_table`).removeClass('hidden');
            if (response_data != '') {
        		$(`#pos_${pos_log_type}_log`).html('');
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var pos_log_id = value.pos_log_id;
                    var pos_activity_type = value.pos_activity_type;
                    var pos_code = value.pos_code;
                    var pos_activity = value.pos_activity;
                    var timestamp = value.timestamp;

                    let pos_log = `
						<tr>
							<td class="break-text">${pos_activity_type}</td>
							<td class="no-break">${pos_code}</td>
							<td class="break-text">${pos_activity}</td>
							<td class="no-break">${timestamp}</td>
						</tr>
                    `;

            		$(`#pos_${pos_log_type}_log`).append(pos_log);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
            }
            else {
        		$(`#${log_type}_log`).html('');
            }
        })
    }
    $('#pos_logs_activator').on('click', function() {
		$('#pos_logs_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_pos_logs();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

	$('#records_tab').on('click', function() {
		load_pos_restocking_codes();
	});

	$('#pos_restocking_type_dropdown').dropdown('set selected', 'daily');

	function load_pos_restocking_codes() {
		let pos_restocking_report_type = $('#pos_restocking_report_type').val();
		let pos_restocking_report_date = $('#pos_restocking_report_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_pos_restocking_codes',
            data  : { 
            	pos_restocking_report_type:pos_restocking_report_type, 
            	pos_restocking_report_date:pos_restocking_report_date
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.pos_restocking_table`).addClass('hidden');
    		$(`#${pos_restocking_report_type}_pos_restocking_table`).removeClass('hidden');

    		$(`.pos_restocking_tbody`).html('');
            if (response_data != '') {
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var pos_restocking_code = value.pos_restocking_code;
                    var total_item_count = value.total_item_count;
                    var pos_restocking_date = value.pos_restocking_date;
                    var pos_restocking_timestamp = value.pos_restocking_timestamp;

                    let pos_report = `
						<tr class="pointered pos_restocking_view" data-pos_restocking_code="${pos_restocking_code}" data-pos_restocking_date="${pos_restocking_date}">
							<td class="no-break">${pos_restocking_code}</td>
							<td class="no-break">${total_item_count}</td>
							<td class="no-break">${pos_restocking_date}</td>
							<td class="no-break">${pos_restocking_timestamp}</td>
						</tr>
                    `;

                    // alert(`#${pos_restocking_report_type}_pos_restocking`)

            		$(`#${pos_restocking_report_type}_pos_restocking`).append(pos_report);
                })

                $('.pos_restocking_view').on('click', function() {
                	let pos_restocking_code = $(this).data('pos_restocking_code');
                	let pos_restocking_date = $(this).data('pos_restocking_date');
                	$('#pos_transaction_view_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: true,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onShow: function() {
			                	load_pos_restocking(pos_restocking_code, pos_restocking_date);
			                    // load_inactive_clients();
					        }
			            })
			            .modal('show')
			        ;
                });
            }
            else {
    			$(`#${pos_restocking_report_type}_pos_restocking_table`).removeClass('hidden');
    			$(`#${pos_restocking_report_type}_pos_restocking`).html('`<tr><td colspan="6" class="center aligned">No records found</td></tr>`');
            }
        })
    }

    function load_pos_restocking(pos_restocking_code, pos_restocking_date) {
    	let view_activity = `Restocking: <u>${pos_restocking_code}</u>`;
    	let transaction_date = `Date: <u>${pos_restocking_date}</u>`;
	    $('#pos_transaction_void_button').data('pos_transaction_code', pos_restocking_code);
	    $('#pos_transaction_void_button').data('pos_transaction_type', 'restocking');

    	$('#pos_transaction_view_activity').html(view_activity);
    	$('#pos_transaction_view_date').html(transaction_date);

        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_pos_restocking',
            data  : { 
            	pos_restocking_code:pos_restocking_code
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {

        		$(`#pos_transaction_view_container`).html('');
        		let final_total = 0;
                $.each(response_data, function(key, value) {
                    var pos_restocking_id = value.pos_restocking_id;
                    var pos_item_name = value.pos_item_name;
                    var pos_item_image = value.pos_item_image;
                    var pos_item_price = value.pos_item_price;
                    var pos_item_count = value.pos_item_count;
                    var pos_item_unit = value.pos_item_unit;
                    var total_price = pos_item_count*pos_item_price;
                    var display_total_price = parseFloat(pos_item_count*pos_item_price).toFixed(2);

                    let formatted_item_price = pos_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    let formatted_total_price = display_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    final_total = final_total + total_price;

                    if (pos_item_count > 1) {
						unit_last = pos_item_unit[pos_item_unit.length-1].toLowerCase();
				        if (
					        unit_last == 's' ||
					        unit_last == 'h' && pos_item_unit.endsWith('sh') ||
					        unit_last == 'h' && pos_item_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        pos_item_unit = pos_item_unit + 'es';
					    } else {
					        pos_item_unit = pos_item_unit + 's';
					    }
					}


                    let view_data = `
						<tr id="pos_restocking_item${pos_restocking_id}" data-pos_restocking_code="${pos_restocking_code}" data-pos_restocking_date="${pos_restocking_date}">
							<td>${key+1}</td>
							<td class="break-text">
	                			<i class="red pointered x icon  void_pos_restocking_item" data-pos_restocking_id="${pos_restocking_id}" data-pos_item_name="${pos_item_name}"></i>
                                <img src="<?php echo base_url();?>photos/pos_images/${pos_item_image}" class="ui avatar image">
                				<span>${pos_item_name}</span>
							</td>
							<td class="no-break">₱${formatted_item_price}</td>
							<td class="no-break">${pos_item_count} ${pos_item_unit}</td>
							<td class="no-break">₱${formatted_total_price}</td>
						</tr>
                    `;

            		$(`#pos_transaction_view_container`).append(view_data);
                });

                $('.void_pos_restocking_item').on('dblclick', function() {
	            	let pos_restocking_id = $(this).data('pos_restocking_id');
	            	let pos_item_name = $(this).data('pos_item_name');
	            	let confirmed = confirm('Are you sure you want to void this restocking item?');
	            	if (confirmed) {
	            		void_pos_restocking_item(pos_restocking_id, pos_item_name);
	            	}
	            });

                let formatted_final_total = parseFloat(final_total).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#pos_transaction_view_total').html('₱'+formatted_final_total);
            }
            else {
        		$(`#pos_transaction_view_container`).html('');
            }
        })
    }

    function void_pos_restocking_item(pos_restocking_id, pos_item_name) {
	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_pos_restocking_item',
	        data  : { 
	            pos_restocking_id: pos_restocking_id
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	alert(`${pos_item_name} was successfully voided from this restocking.`);
	        	let pos_restocking_code = $(`#pos_restocking_item${pos_restocking_id}`).data('pos_restocking_code');
	        	let pos_restocking_date = $(`#pos_restocking_item${pos_restocking_id}`).data('pos_restocking_date');

	        	load_pos_restocking(pos_restocking_code, pos_restocking_date);
            	load_pos_inventory();
	        }
	        else if (response == 'success-null') {
	        	$('#pos_transaction_view_modal').modal('hide');
	        	alert(`${pos_item_name} was successfully voided. All items from this restock has been voided.`);
	        	load_pos_restocking_codes();
            	load_pos_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

	$('#pos_transaction_void_button').on('dblclick', function() {
		let pos_transaction_code = $(this).data('pos_transaction_code');
		let pos_transaction_type = $(this).data('pos_transaction_type');
    	let confirmed = confirm('Are you sure you want to void this transaction and all related items? Once voided, it cannot be restored.');
    	if (confirmed) {
    		if (pos_transaction_type == 'checkout') {
	    		void_pos_checkout(pos_transaction_code);
    		}
    		else if (pos_transaction_type == 'restocking') {
	    		void_pos_restocking(pos_transaction_code);
    		}
    	}
	});

	function void_pos_restocking(pos_restocking_code) {
		var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_pos_restocking',
	        data  : { 
	            pos_restocking_code: pos_restocking_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	$('#pos_transaction_view_modal').modal('hide');
	        	alert(`Restocking with code "${pos_restocking_code}" was successfully voided.`);
	        	load_pos_restocking_codes();
	            load_pos_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

    $('#pos_restocking_type_dropdown,#pos_restocking_report_date').on('change', function() {
    	load_pos_restocking_codes();
	});

	$('#pos_restocking_items_drop, #pos_restock_quantity').on('keydown', function(e) {
	    if (e.key === 'Enter') {
	        $('#pos_restocking_form').submit();
	    }
	});


    $('#pos_logs_activator').on('click', function() {
		$('#pos_logs_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_pos_logs();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

    $('#pos_checkout_submit').on('dblclick', function(e) {
	    e.preventDefault();

	    if (!pos_cart_array || pos_cart_array.length === 0) {
	        alert('Cart is empty.');
	        return;
	    }

	    let formData = new FormData();
	    formData.append('cart_items', JSON.stringify(pos_cart_array)); // send cart as JSON string

	    $.ajax({
	        url: '<?php echo base_url(); ?>i.php/sys_control/pos_checkout',
	        method: 'POST',
	        data: formData,
	        processData: false,  // important for FormData
	        contentType: false,  // important for FormData
	        success: function (response) {
	            if (response === 'success') {
	                pos_cart_array = []; // clear current cart
			    	$('#pos_checkout_cart_empty_message').addClass('hidden');
			    	$('#pos_checkout_cart_empty_message').removeClass('visible');
			    	$('#pos_checkout_cart_content').html('');
	                alert('Checkout successful!');
	            } 
	            else if (response === 'empty_cart') {
	                alert('Cart is empty. Nothing to checkout.');
	            }
	            else {
	                alert('Checkout failed. Try again.');
	            }
	        },
	        error: function (xhr, status, error) {
	            console.error(xhr.responseText);
	            alert('AJAX error during checkout.');
	        }
	    });
	});


    $('.new_pos_item_activator').on('click', function (event) {
    	toggle_new_pos_capture_button();
        $('#new_pos_item_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onHide: function() {
		            stop_new_pos_camera();
		        },
		        onShow: function(){
		        	start_new_pos_camera();
		        }
            })
            .modal('show')
        ;
    });

    function initialize_pos_item_camera() {
	    const video = $('#new_pos_camera_stream')[0];
	    const canvas = $('#new_pos_captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#new_pos_capture_button');
	    const retake_button = $('#new_pos_retake_button');
	    const file_input = $('#new_pos_image_file')[0];
	    const uploaded_preview = $('#new_pos_uploaded_preview');

	    // --- Setup video element ---
	    $('#new_pos_camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover',
	        'cursor': 'pointer',
	        'pointer-events': 'auto'
	    });

	    // --- Handle camera or upload click ---
	    $('#new_pos_camera_stream, #new_pos_camera_container').off('dblclick').on('dblclick', function () {
	        console.log('Double-click detected — opening file chooser');
	        file_input.click();
	    });

	    // --- File input handling ---
	    file_input.addEventListener('change', function (e) {
	        const file = e.target.files[0];
	        if (!file) return;

	        const reader = new FileReader();
	        reader.onload = function (ev) {
	            // Hide video & canvas, show uploaded preview
	            $('#new_pos_camera_stream, #new_pos_captured_canvas').hide();
	            uploaded_preview.attr('src', ev.target.result).show();

	            // Generate filename
	            const item_name = $('#new_pos_item_name').val().replace(/\s+/g, '_');
	            const timestamp = Date.now();
	            const file_name = `${item_name}_${timestamp}.png`;

	            // Assign to hidden inputs
	            $('#new_pos_item_image').val(ev.target.result);
	            $('#new_pos_item_image_name').val(file_name).removeClass('invisible');

	            capture_button.hide();
	            retake_button.show();
	        };
	        reader.readAsDataURL(file);
	    });

	    // --- Capture from camera ---
	    capture_button.off('click').on('click', function() {
	        const item_name = $('#new_pos_item_name').val().replace(/\s+/g, '_');
	        const timestamp = Date.now();
	        const file_name = `${item_name}_${timestamp}.png`;

	        $('#new_pos_item_image_name').val(file_name).removeClass('invisible');

	        const aspect_w = 5, aspect_h = 4;
	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;
	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#new_pos_camera_stream').hide();
	        $('#new_pos_captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#new_pos_item_image').val(canvas.toDataURL('image/png'));
	    });

	    // --- Retake logic ---
	    retake_button.off('click').on('click', function() {
	        $('#new_pos_captured_canvas, #new_pos_uploaded_preview').hide();
	        $('#new_pos_camera_stream').show();
	        capture_button.show();
	        retake_button.hide();

	        $('#new_pos_item_image').val('');
	        $('#new_pos_item_image_name').val('').addClass('invisible');
	        file_input.value = '';
	    });
	}
	function initialize_pos_item_update_camera() {
	    const video = $('#update_pos_camera_stream')[0];
	    const canvas = $('#update_pos_captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#update_pos_capture_button');
	    const retake_button = $('#update_pos_retake_button');
	    const file_input = $('#update_pos_image_file')[0];
	    const uploaded_preview = $('#update_pos_uploaded_preview');

	    // --- Setup video element ---
	    $('#update_pos_camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover',
	        'cursor': 'pointer',
	        'pointer-events': 'auto'
	    });

	    // --- Handle camera or upload click ---
	    $('#update_pos_camera_stream, #update_pos_camera_container').off('dblclick').on('dblclick', function () {
	        console.log('Double-click detected — opening file chooser');
	        file_input.click();
	    });

	    // --- File input handling ---
	    file_input.addEventListener('change', function (e) {
	        const file = e.target.files[0];
	        if (!file) return;

	        const reader = new FileReader();
	        reader.onload = function (ev) {
	            // Hide video & canvas, show uploaded preview
	            $('#update_pos_camera_stream, #update_pos_captured_canvas').hide();
	            uploaded_preview.attr('src', ev.target.result).show();

	            // Generate filename
	            const item_name = $('#update_pos_item_name').val().replace(/\s+/g, '_');
	            const timestamp = Date.now();
	            const file_name = `${item_name}_${timestamp}.png`;

	            // Assign to hidden inputs
	            $('#update_pos_item_image').val(ev.target.result);
	            $('#update_pos_item_image_name').val(file_name).removeClass('invisible');

	            capture_button.hide();
	            retake_button.show();
	        };
	        reader.readAsDataURL(file);
	    });

	    // --- Capture from camera ---
	    capture_button.off('click').on('click', function() {
	        const item_name = $('#update_pos_item_name').val().replace(/\s+/g, '_');
	        const timestamp = Date.now();
	        const file_name = `${item_name}_${timestamp}.png`;

	        $('#update_pos_item_image_name').val(file_name).removeClass('invisible');

	        const aspect_w = 5, aspect_h = 4;
	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;
	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#update_pos_camera_stream').hide();
	        $('#update_pos_captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#update_pos_item_image').val(canvas.toDataURL('image/png'));
	    });

	    // --- Retake logic ---
	    retake_button.off('click').on('click', function() {
	        $('#update_pos_captured_canvas, #update_pos_uploaded_preview').hide();
	        $('#update_pos_camera_stream').show();
	        capture_button.show();
	        retake_button.hide();

	        $('#update_pos_item_image').val('');
	        $('#update_pos_item_image_name').val('').addClass('invisible');
	        file_input.value = '';
	    });

	    $('#update_pos_item_image_button').on('click', function() {
	    	$('#current_pos_image_field').addClass('invisible');
	    	$('#update_pos_webcam_field').removeClass('invisible');
            start_update_pos_camera();

	    });
	    $('#cancel_pos_image_update').on('click', function() {
	    	$('#current_pos_image_field').removeClass('invisible');
	    	$('#update_pos_webcam_field').addClass('invisible');
            stop_update_pos_camera();
	    });
	}

	$(document).ready(function() {
		initialize_time_manager_camera();
		initialize_time_manager_update_camera();
		initialize_pos_item_camera();
		initialize_pos_item_update_camera();
		initialize_supply_item_camera();
		initialize_supply_item_update_camera();

		monitor_item_stock(function(el) {
		    var current_stock = parseFloat(el.textContent.trim());
		    var low_stock = parseFloat(el.dataset.item_low);
		    var item_id = parseFloat(el.dataset.item_id);

		    // Example: log it
		    console.log("Stock changed:", current_stock);

		    // Example: add a class if low
		    if (current_stock <= low_stock) {
		        el.classList.add('low_stock');
		    } else {
		        el.classList.remove('low_stock');
		    }
		});
	});

	function toggle_new_pos_capture_button() {
	    let item_name = $('input[name="new_pos_item_name"]').val();

	    if (item_name !== '') {
	        $('#new_pos_capture_button').show();
	    } else {
	        $('#new_pos_capture_button').hide();
	    }
	}

	$('input[name="new_pos_item_name"]').on('change', toggle_new_pos_capture_button);

	$('#new_pos_item_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();
	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/new_pos_item',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        alert('New Item added successfully.')
	                        $('#new_pos_item_modal').modal('hide');
	                    	load_pos_inventory();
	                    }
	                    else {
	                        alert('Time Profile creation failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            new_pos_item_name: {
	                identifier: 'new_pos_item_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Name.'
	                    }
	                ]
	            },
	            new_pos_item_price: {
	                identifier: 'new_pos_item_price',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Number.'
	                    }
	                ]
	            },
	            new_pos_item_image_name: {
	                identifier: 'new_pos_item_image_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Image.'
	                    }
	                ]
	            },
	            new_pos_item_stock: {
	                identifier: 'new_pos_item_stock',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Num.'
	                    }
	                ]
	            },
	            new_pos_item_unit: {
	                identifier: 'new_pos_item_unit',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Number.'
	                    }
	                ]
	            },
	            new_pos_item_image: {
	                identifier: 'new_pos_item_image',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Image.'
	                    }
	                ]
	            }
	        }
	    })
	;

	$('#update_pos_item_form')
    .form({
        on: 'change',
        inline: false,
        transition: 'fade',
        onSuccess: function(event) {
            event.preventDefault();

            var ajax = $.ajax({
                method: 'POST',
                url   : '<?php echo base_url();?>i.php/sys_control/update_pos_item',
                data  : new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            var jqxhr = ajax
                .always(function() {
                    var response = jqxhr.responseText.trim();
                    if (response == 'success') {
                        alert('Item Updated Successfully.');
                        $('#update_pos_item_modal').modal('hide');
                    	load_pos_inventory();
                    } 
                    else {
                        alert('Update Failed. Please try again.');
                    }
                });
        },
        fields: {
            update_pos_item_name: {
                identifier: 'update_pos_item_name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter the item name.'
                    }
                ]
            },
            update_pos_item_price: {
                identifier: 'update_pos_item_price',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter a price.'
                    },
                    {
                        type: 'decimal',
                        prompt: 'Price must be a valid number.'
                    }
                ]
            },
            update_pos_item_unit: {
                identifier: 'update_pos_item_unit',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter the unit (e.g. piece, box).'
                    }
                ]
            },
            update_pos_item_stock: {
                identifier: 'update_pos_item_stock',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter current stock.'
                    },
                    {
                        type: 'integer',
                        prompt: 'Stock must be a whole number.'
                    }
                ]
            },
            update_pos_item_low: {
                identifier: 'update_pos_item_low',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter low stock level.'
                    },
                    {
                        type: 'integer',
                        prompt: 'Low stock level must be a whole number.'
                    }
                ]
            }
        }
    });

    let new_pos_camera_stream = null;
	function start_new_pos_camera() {
		const video = $('#new_pos_camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            new_pos_camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_new_pos_camera() {
	    if (new_pos_camera_stream) {
	        new_pos_camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        new_pos_camera_stream = null;
	    }

	    const video = $('#new_pos_camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}

	let update_pos_camera_stream = null;
	function start_update_pos_camera() {
		const video = $('#update_pos_camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            update_pos_camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_update_pos_camera() {
	    if (update_pos_camera_stream) {
	        update_pos_camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        update_pos_camera_stream = null;
	    }

	    const video = $('#update_pos_camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}


































	$('#supply_checkout_cart_activator').on('click', function() {
		$('#supply_checkout_cart_modal')
		.modal({
	        useFlex: true,
	        allowMultiple: false,
	        autofocus: false,
	        blurring: true,
	        closable: false,
	        onHide: function() {
	            // stop_update_supply_camera();
	        },
	        onShow: function(){
	        	// start_update_supply_camera();
	        }
	    })
	    .modal('show')

	})
	
	$('#supply_checkouts_type_dropdown').dropdown('set selected', 'daily');

	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0];
	    document.getElementById('supply_checkouts_date').value = today;
	});

	$('#supply_checkouts_activator').on('click', function() {
		$('#supply_checkouts_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_supply_checkout_codes();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});
	$('#supply_restocking_activator').on('click', function() {
		$('#supply_restocking_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	// load_supply_checkouts();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

	function load_supply_checkout_codes() {
	    let supply_checkouts_type = $('#supply_checkouts_type').val();
	    let supply_checkouts_date = $('#supply_checkouts_date').val();

	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/load_supply_checkout_codes',
	        data  : { 
	            supply_checkouts_type: supply_checkouts_type, 
	            supply_checkouts_date: supply_checkouts_date
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response_data = JSON.parse(jqxhr.responseText);

	        $('.supply_checkouts_table').addClass('hidden');
	        $(`#${supply_checkouts_type}_supply_checkouts_table`).removeClass('hidden');

	        $('.supply_checkouts_tbody').html('');

	        if (response_data != '') {

	            $.each(response_data, function(key, value) {
	                var supply_checkout_code = value.supply_checkout_code;
	                var total_item_count  = value.total_item_count;
	                var supply_checkout_date = value.supply_checkout_date;

	                let supply_report = `
	                    <tr class="pointered supply_checkout_view" 
	                        data-supply_checkout_code="${supply_checkout_code}" data-supply_checkout_date="${supply_checkout_date}">
	                        
	                        <td class="no-break">${supply_checkout_code}</td>
	                        <td class="no-break">${total_item_count}</td>
	                        <td class="no-break">${supply_checkout_date}</td>
	                    </tr>
	                `;

	                $(`#${supply_checkouts_type}_supply_checkouts`).append(supply_report);
	            });

	            $('.supply_checkout_view').on('click', function() {
	                let supply_checkout_code = $(this).data('supply_checkout_code');
	                let supply_checkout_date = $(this).data('supply_checkout_date');

	                $('#supply_transaction_view_modal')
	                    .modal({
	                        useFlex: true,
	                        allowMultiple: true,
	                        autofocus: false,
	                        blurring: true,
	                        closable: false,
	                        onShow: function() {
	                            load_supply_checkout(supply_checkout_code, supply_checkout_date);
	                        }
	                    })
	                    .modal('show');
	            });
	        }
	        else {
	            $(`#${supply_checkouts_type}_supply_checkouts_table`).removeClass('hidden');
	            $(`#${supply_checkouts_type}_supply_checkouts`).html(
	                '<tr><td colspan="6" class="center aligned">No records found</td></tr>'
	            );
	        }
	    });
	}
	function load_supply_checkout(supply_checkout_code, supply_checkout_date) {
	    let view_activity = `Checkout: <u>${supply_checkout_code}</u>`;
	    let transaction_date = `Date: <u>${supply_checkout_date}</u>`;
	    $('#supply_transaction_void_button').data('supply_transaction_code', supply_checkout_code);
	    $('#supply_transaction_void_button').data('supply_transaction_type', 'checkout');

	    $('#supply_transaction_view_activity').html(view_activity);
	    $('#supply_transaction_view_date').html(transaction_date);

	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/load_supply_checkout',
	        data  : { 
	            supply_checkout_code: supply_checkout_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response_data = JSON.parse(jqxhr.responseText);

	        if (response_data != '') {

	            $('#supply_transaction_view_container').html('');
	            let final_total = 0;

	            $.each(response_data, function(key, value) {
	                var supply_checkout_id  = value.supply_checkout_id;
	                var supply_item_name  = value.supply_item_name;
	                var supply_item_image = value.supply_item_image;
	                var supply_item_price = value.supply_item_price;
	                var supply_item_count = value.supply_item_count;
	                var supply_item_unit  = value.supply_item_unit;

	                var total_price = supply_item_count * supply_item_price;
	                var display_total_price = parseFloat(total_price).toFixed(2);

	                let formatted_item_price  = supply_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	                let formatted_total_price = display_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	                final_total += total_price;

	                // pluralize units
	                if (supply_item_count > 1) {
	                    unit_last = supply_item_unit[supply_item_unit.length - 1].toLowerCase();

	                    if (
	                        unit_last == 's' ||
	                        unit_last == 'h' && supply_item_unit.endsWith('sh') ||
	                        unit_last == 'h' && supply_item_unit.endsWith('ch') ||
	                        unit_last == 'x' ||
	                        unit_last == 'z'
	                    ) {
	                        supply_item_unit = supply_item_unit + 'es';
	                    } else {
	                        supply_item_unit = supply_item_unit + 's';
	                    }
	                }

	                let view_data = `
						<tr id="supply_checkout_item${supply_checkout_id}" data-supply_checkout_code="${supply_checkout_code}" data-supply_checkout_date="${supply_checkout_date}">
	                        <td>${key+1}</td>
	                        <td class="break-text">
	                			<i class="red pointered x icon  void_supply_checkout_item" data-supply_checkout_id="${supply_checkout_id}" data-supply_item_name="${supply_item_name}"></i>
	                            <img src="<?php echo base_url();?>photos/supply_images/${supply_item_image}" class="ui avatar image">
	                            <span>${supply_item_name}</span>
	                        </td>
	                        <td class="no-break">₱${formatted_item_price}</td>
	                        <td class="no-break">${supply_item_count} ${supply_item_unit}</td>
	                        <td class="no-break">₱${formatted_total_price}</td>
	                    </tr>
	                `;

	                $('#supply_transaction_view_container').append(view_data);
	            });

	            $('.void_supply_checkout_item').on('dblclick', function() {
	            	let supply_checkout_id = $(this).data('supply_checkout_id');
	            	let supply_item_name = $(this).data('supply_item_name');
	            	let confirmed = confirm('Are you sure you want to void this checkout item?');
	            	if (confirmed) {
	            		void_supply_checkout_item(supply_checkout_id, supply_item_name);
	            	}
	            });
	            let formatted_final_total = parseFloat(final_total).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#supply_transaction_view_total').html('₱'+formatted_final_total);

	        } else {

	            $('#supply_transaction_view_container').html('');

	        }
	    });
	}

	function void_supply_checkout_item(supply_checkout_id, supply_item_name) {
	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_supply_checkout_item',
	        data  : { 
	            supply_checkout_id: supply_checkout_id
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
        	if (response == 'success') {
	        	alert(`${supply_item_name} was successfully voided from this checkout.`);
	        	let supply_checkout_code = $(`#supply_checkout_item${supply_checkout_id}`).data('supply_checkout_code');
	        	let supply_checkout_date = $(`#supply_checkout_item${supply_checkout_id}`).data('supply_checkout_date');

	        	load_supply_checkout(supply_checkout_code, supply_checkout_date);
            	load_supply_inventory();
	        }
	        else if (response == 'success-null') {
	        	$('#supply_transaction_view_modal').modal('hide');
	        	alert(`${supply_item_name} was successfully voided. All items from this checkout has been voided.`);
	        	load_supply_checkout_codes();
            	load_supply_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

	function void_supply_checkout(supply_checkout_code) {
		var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_supply_checkout',
	        data  : { 
	            supply_checkout_code: supply_checkout_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	$('#supply_transaction_view_modal').modal('hide');
	        	alert(`Checkout with code "${supply_checkout_code}" was successfully voided.`);
	        	load_supply_checkout_codes();
            	load_supply_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}


	// Auto load on page open
	$('#supply_checkouts_modal').modal({
	    onShow: function() {
	        load_supply_checkout_codes();
	    }
	});

	// Reload when filters change
	$('#supply_checkouts_type_dropdown').dropdown({
	    onChange: function () {
	        load_supply_checkout_codes();
	    }
	});

	$('#supply_checkouts_date').on('change', function () {
	    load_supply_checkout_codes();
	});


	let supply_cart_array = [];
	function load_supply_inventory() {
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_supply_inventory'
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
			    var search_content = [];
        		$(`#supply_items_container`).html('');
                $('#supply_empty_message').addClass('invisible');
                $.each(response_data, function(key, value) {
                    let supply_item_id = value.supply_item_id;
                    let supply_item_name = value.supply_item_name;
                    let supply_item_price = value.supply_item_price;
                    let supply_item_image = value.supply_item_image;
                    let supply_item_stock = value.supply_item_stock;
                    let supply_item_unit = value.supply_item_unit;
                    let supply_item_low = value.supply_item_low;
                    let supply_item_status = value.supply_item_status;


                    let default_supply_item_unit = supply_item_unit;

                    let formatted_item_price = supply_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");


                    if (supply_item_stock > 1) {
						unit_last = supply_item_unit[supply_item_unit.length-1].toLowerCase();
				        if (
					        unit_last == 's' ||
					        unit_last == 'h' && supply_item_unit.endsWith('sh') ||
					        unit_last == 'h' && supply_item_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        supply_item_unit = supply_item_unit + 'es';
					    } else {
					        supply_item_unit = supply_item_unit + 's';
					    }
					}

                    let supply_item = `
						<div class="ui fluid link card transition item_card" id="supply_item${supply_item_id}" data-supply_item_id="${supply_item_id}" data-supply_item_name="${supply_item_name}" data-supply_item_price="${supply_item_price}" data-supply_item_image="${supply_item_image}" data-supply_item_stock="${supply_item_stock}" data-supply_item_unit="${default_supply_item_unit}" data-supply_item_low="${supply_item_low}" data-supply_item_status="${supply_item_status}">
						    <div class="blurring dimmable image image-container">
								<div class="ui dimmer">
									<div class="content">
                    					<div class="ui blue mini inverted button edit_supply_item">
											Edit
										</div>
                    					<br><br>
                    					<div class="ui orange mini inverted button archive_supply_item invisible">
											Archive
										</div>
									</div>
								</div>
                            	<img src="<?php echo base_url();?>photos/supply_images/${supply_item_image}">
							</div>
						    <div class="content">
					    		<input type="text" class="supply_card_focus_handler file_input" data-item_id="${supply_item_id}">
						    	<div class="item">
									<h5 class="no-break supply_item_name" data-content="${supply_item_name}" data-position="bottom left">${supply_item_name}</h5>
								    <div class="content">
										<a class="ui tiny grey header"><x class="item_stock supply_item_stock" data-item_low="${supply_item_low}" data-item_id="${supply_item_id}">${supply_item_stock}</x> <x class="supply_item_unit">${supply_item_unit}</x></a>
								    </div>
								</div>
						    </div>
						    <div class="extra content">
						    	<div class="ui left floated small buttons">
									<button class="ui basic compact mini button supply_count_minus">
										<i class="left floated small minus icon"></i>
									</button>
									<div class="or supply_count_container" data-text="0" data-max_limit="${supply_item_stock}"></div>
									<button class="ui basic compact mini button supply_count_plus">
										<i class="right floated small plus icon"></i>
									</button>
								</div>
								<div class="ui right floated basic small button add_to_supply_cart invisible">
									Add
									<i class="right cart plus icon"></i>
								</div>
						    </div>
						</div>
                    `;

                    let supply_restocking_item = `
                    	<div class="item supply_restocking_item" data-value="${supply_item_id}" data-supply_item_name="${supply_item_name}" data-supply_item_image="${supply_item_image}">
							<img class="ui mini item_avatar image" src="<?php echo base_url();?>photos/supply_images/${supply_item_image}">
							<span>${supply_item_name}</span>
						</div>
                    `;

            		$(`#supply_items_container`).append(supply_item);
            		$(`#supply_restocking_menu`).append(supply_restocking_item);
		      		search_content.push({id:supply_item_id,title:supply_item_name,description:"₱ "+formatted_item_price});
                })

                $('.supply_item_name').popup({
                	on: 'click'
                });
				let supply_restocking_array = [];
				let supply_restocking_list_item = '';
				let supply_list_restock_date = '';
				let supply_list_item_id = '';
				let supply_list_item_name = '';
				let supply_list_item_image = '';

				$('#supply_restocking_items_drop').dropdown({
				    action: function (text, value, element) {
				    	$('#supply_restocking_menu .supply_restocking_item').removeClass('invisible');
						$('#supply_restocking_menu .supply_restocking_item').addClass('item');

				        supply_restocking_array.forEach(function(item) {
			                let element = $(`#supply_restocking_items_drop [data-value='${item.supply_item_id}']`);

			                element.addClass('invisible');
			                element.removeClass('active');
							element.removeClass('item');
						});
						$(this).dropdown('set selected', value);
						$(this).dropdown('hide');
						supply_list_item_id = value;
						supply_list_item_name = $(element).data('supply_item_name');
						supply_list_item_image = $(element).data('supply_item_image');
						$('#supply_restock_quantity').trigger('focus');
				    },
				    forceSelection: false
				});
				// $('#supply_restocking_insert').on('click', function(){
					
				// });

				$('#supply_restocking_form')
				    .form({
				        on: 'change',
				        inline: false,
				        transition: 'fade',
				        onSuccess: function(event) {
				            event.preventDefault();

				            let supply_list_restock_date = $('#supply_restocking_date').val();
							let supply_list_restock_quantity = $('#supply_restock_quantity').val();
							let errors = [];

							if (!supply_list_restock_date) {
								errors.push("Restocking Date");
							}
							if (!supply_list_item_id) {
								errors.push("Item");
							}
							if (!supply_list_restock_quantity) {
								errors.push("Quantity");
							}

							if (errors.length > 0) {
								if (errors.length === 1) {
									alert(`Please provide a valid ${errors[0]}.`);
								} else if (errors.length === 2) {
									alert(`Please provide valid ${errors[0]} and ${errors[1]}.`);
								} else {
									alert(`Please provide valid ${errors.join(", ")}.`);
								}
								return; // Stop execution if there are errors
							}

							if (supply_list_restock_quantity <= 0) {
								alert("Please provide a valid quantity.")
							}
							else {
								supply_restocking_list_item = `
									<div class="item" data-supply_item_id="${supply_list_item_id}">
										<div class="right floated content">
						                	<i class="ui x red icon pointered supply_restocking_item_remover"></i>
										</div>
			                        	<img class="ui mini item_avatar image" src="<?php echo base_url();?>photos/supply_images/${supply_list_item_image}">
								    	<div class="content">
									      	<a class="header">${supply_list_item_name}</a>
									    	<div class="description">
												x${supply_list_restock_quantity}
									    	</div>
									    </div>
									</div>
								`;
								$('#supply_restocking_list').append(supply_restocking_list_item);	

								$('.supply_restocking_item_remover').on('dblclick', function() {
									confirmed = confirm('Remove this restocking item?');
									if (confirmed) {
									    const item = $(this).closest('.item');
									    const supply_item_id = item.data('supply_item_id');
									    $(`#supply_restocking_list .item[data-supply_item_id='${supply_item_id}']`).remove();

									    for (let i = 0; i < supply_restocking_array.length; i++) {
								            if (supply_restocking_array[i].supply_item_id == supply_item_id) { // use == to allow type coercion
								                supply_restocking_array.splice(i, 1);
								                break;
								            }
								        }

										$('#supply_restocking_menu .supply_restocking_item').removeClass('invisible');
										$('#supply_restocking_menu .supply_restocking_item').addClass('item');
									    supply_restocking_array.forEach(function(item) {
							                let element = $(`#supply_restocking_items_drop [data-value='${item.supply_item_id}']`);

							                element.addClass('invisible');
							                element.removeClass('active');
											element.removeClass('item');
										});

										if ($('#supply_restocking_menu .item').length === $('#supply_restocking_menu .item.invisible').length) {
										    $('#supply_restocking_items_drop').addClass('disabled');
										} 
										else {
										    $('#supply_restocking_items_drop').removeClass('disabled');
										}
									}
								});

								supply_restocking_array.push({
								    supply_item_id: supply_list_item_id,
								    supply_item_count: supply_list_restock_quantity
								});
								$('#supply_restocking_menu .supply_restocking_item').removeClass('invisible');
								$('#supply_restocking_menu .supply_restocking_item').addClass('item');
								supply_restocking_array.forEach(function(item) {
									// alert(item.supply_item_id)
					                let element = $(`#supply_restocking_items_drop [data-value='${item.supply_item_id}']`);

					                element.addClass('invisible');
					                element.removeClass('active');
									element.removeClass('item');
								});

								if ($('#supply_restocking_menu .item').length === $('#supply_restocking_menu .item.invisible').length) {
								    $('#supply_restocking_items_drop').addClass('disabled');
								} 
								else {
								    $('#supply_restocking_items_drop').removeClass('disabled');
								}

								// $('#supply_restocking_form').form('reset');
								$('#supply_restocking_items_drop').dropdown('clear');
								$('#supply_restock_quantity').val('');

								$('.supply_restocking_divider').removeClass('invisible');
							}
							if (supply_restocking_array.length > 0) {
								$('#supply_restocking_submit').removeClass('invisible');
							}

	        				$('#supply_restocking_items_drop .search').focus();
				        },
				        fields: {
				            supply_restocking_date: {
				                identifier: 'supply_restocking_date',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            },
				            supply_restocking_items_drop: {
				                identifier: 'supply_restocking_items_drop',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            },
				            supply_restock_quantity: {
				                identifier: 'supply_restock_quantity',
				                rules: [
				                    {
				                        type: 'empty',
				                        prompt: ''
				                    }
				                ]
				            }
				        }
				    })
				;

				$('#supply_restocking_submit').on('dblclick', function(e) {
				    e.preventDefault();

				    if (!supply_restocking_array || supply_restocking_array.length === 0) {
				        alert('Cart is empty.');
				        return;
				    }

				    let formData = new FormData();
				    formData.append('supply_restocking_items', JSON.stringify(supply_restocking_array)); // send cart as JSON string
					formData.append('supply_restocking_date', $('#supply_restocking_date').val());
				    
				    $.ajax({
				        url: '<?php echo base_url(); ?>i.php/sys_control/supply_restock',
				        method: 'POST',
				        data: formData,
				        processData: false,  // important for FormData
				        contentType: false,  // important for FormData
				        success: function (response) {
				            if (response === 'success') {
				                supply_restocking_array = []; // clear current cart
						    	$('#supply_checkout_cart_empty_message').addClass('hidden');
						    	$('#supply_checkout_cart_empty_message').removeClass('visible');
						    	$('#supply_checkout_cart_content').html('');
				                alert('Restocking successful! Inventory content will reload shortly...');
				                load_supply_inventory();
				                $('#supply_restocking_modal').modal('hide')
				            } 
				            else if (response === 'empty_cart') {
				                alert('Restocking is empty. Nothing to insert.');
				            }
				            else {
				                alert('Restocking failed. Try again.');
				            }
				        },
				        error: function (xhr, status, error) {
				            console.error(xhr.responseText);
				            alert('AJAX error during checkout.');
				        }
				    });
				});

                $('#supply_inventory_search')
					.search({
						source: search_content,
						fullTextSearch: 'exact',
						maxResults: 100,
						minCharacters: 2,
						showNoResults: false,
						onSelect: function(result, response) {
							search_supply_item_id = result.id;
							$('.supply_card_focus_handler[data-item_id="' + result.id + '"]').focus();

							$('#supply_item'+search_supply_item_id)
								.transition('pulse')
								.transition('flash')
							;
						}
					})
				;

                function open_supply_item_update_modal(data) {
			        $('#update_supply_item_id').val(data.supply_item_id);
			        $('#update_supply_item_name').val(data.supply_item_name);
			        $('#update_supply_item_price').val(data.supply_item_price);
			        $('#update_supply_item_stock').val(data.supply_item_stock);
			        $('#update_supply_item_unit').val(data.supply_item_unit);
			        $('#update_supply_item_low').val(data.supply_item_low);
			        $('#current_supply_item_image').attr('src', data.supply_item_image_url);

			        $('#update_supply_item_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: false,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onHide: function() {
					            // stop_update_supply_camera();
					        },
					        onShow: function(){
					        	// start_update_supply_camera();
					        }
			            })
			            .modal('show')
			        ;
			    }
                $('.edit_supply_item').on('click', function() {
					let card = $(this).closest('.card'); // go up to the parent card

					let supply_item_id = card.data('supply_item_id');
					let supply_item_name = card.data('supply_item_name');
					let supply_item_price = card.data('supply_item_price');
					let supply_item_image = card.data('supply_item_image');
					let supply_item_stock = card.data('supply_item_stock');
					let supply_item_unit = card.data('supply_item_unit');
					let supply_item_low = card.data('supply_item_low');
					let supply_item_status = card.data('supply_item_status');

			        open_supply_item_update_modal({
				        supply_item_id: supply_item_id,
				        supply_item_name: supply_item_name,
				        supply_item_price: supply_item_price,
				        supply_item_image: supply_item_image,
				        supply_item_stock: supply_item_stock,
				        supply_item_unit: supply_item_unit,
				        supply_item_low: supply_item_low,
				        supply_item_status: supply_item_status,
				        supply_item_image_url: `<?php echo base_url();?>photos/supply_images/${supply_item_image}`
				    });
        		});
        		$('.supply_count_minus, .supply_count_plus').on('click', function() {
					let container = $(this).siblings('.supply_count_container');
					let item_count = Number(container.attr('data-text'));
					let max_limit = Number(container.attr('data-max_limit'));
					// let max_limit = supply_item_stock;
					let card = $(this).closest('.card');
					let stock_el = card.find('.supply_item_stock').first();
					let current_stock = Number(stock_el.text());

					let unit_el = card.find('.supply_item_unit').first();
					let current_unit = unit_el.text();
					let add_button = card.find('.add_to_supply_cart');

					if ($(this).hasClass('supply_count_plus')) {
						if (item_count < max_limit && current_stock > 0) {
							item_count++;
							current_stock--;
						}
					} else {
						if (item_count > 0) {
							item_count--;
							current_stock++;
						}
					}

					container.attr('data-text', item_count);
					stock_el.text(current_stock);
					item_count > 0 ? add_button.removeClass('invisible') : add_button.addClass('invisible');

					if (current_unit.endsWith('es')) {
					    if (/(sh|ch|x|z|s)es$/.test(current_unit)) {
					        current_unit = current_unit.slice(0, -2);
					    } else if (!current_unit.endsWith('ses')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					} else if (current_unit.endsWith('s')) {
					    if (!current_unit.endsWith('ss')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					}

					if (current_stock != 1) {
					    unit_last = current_unit[current_unit.length - 1].toLowerCase();
					    if (
					        unit_last == 's' ||
					        unit_last == 'h' && current_unit.endsWith('sh') ||
					        unit_last == 'h' && current_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        current_unit = current_unit + 'es';
					    } 
					    else {
					        current_unit = current_unit + 's';
					    }
					} 
					else {
					    if (current_unit.endsWith('es')) {
					        current_unit = current_unit.slice(0, -2);
					    } 
					    else if (current_unit.endsWith('s')) {
					        current_unit = current_unit.slice(0, -1);
					    }
					}

					unit_el.text(current_unit);
				});

				$('.add_to_supply_cart').on('click', function() {
				    let card = $(this).closest('.card');
				    let add_button = card.find('.add_to_supply_cart');
				    let supply_checkout_cart_el = $('#supply_checkout_cart_activator');

				    let item_count_el = card.find('.supply_count_container').first();
				    let item_stock_el = card.find('.supply_item_stock').first();

				    let supply_item_id     = card.data('supply_item_id');
				    let supply_item_name   = card.data('supply_item_name');
				    let supply_item_price  = card.data('supply_item_price');
				    let supply_item_image  = card.data('supply_item_image');
				    let supply_item_stock  = card.data('supply_item_stock');
				    let supply_item_unit   = card.data('supply_item_unit');
				    let supply_item_low    = card.data('supply_item_low');
				    let supply_item_status = card.data('supply_item_status');

				    let item_stock = item_stock_el.text();
				    let item_count = item_count_el.attr('data-text');
				    let remaining = item_stock;
				    let total_item_price = supply_item_price * item_count;

				    let formatted_item_price = parseFloat(total_item_price).toFixed(2);
				    formatted_item_price = formatted_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

				    item_stock_el.text(remaining);
				    item_count_el.attr('data-text', 0);
				    add_button.addClass('invisible');

				    card.transition({
				        animation: 'bounce',
				        onComplete: function() {
				            supply_checkout_cart_el.transition('jiggle')
				        }
				    });

				    if (item_count > 1) {
				        unit_last = supply_item_unit[supply_item_unit.length-1].toLowerCase();
				        if (
				            unit_last == 's' ||
				            unit_last == 'h' && supply_item_unit.endsWith('sh') ||
				            unit_last == 'h' && supply_item_unit.endsWith('ch') ||
				            unit_last == 'x' ||
				            unit_last == 'z'
				        ) {
				            supply_item_unit = supply_item_unit + 'es';
				        } else {
				            supply_item_unit = supply_item_unit + 's';
				        }
				    }

				    supply_cart_array.push({
					    supply_item_id: supply_item_id,
					    supply_item_name: supply_item_name,
					    supply_item_price: supply_item_price,
					    item_count: item_count,
					    supply_item_unit: supply_item_unit,
					    supply_item_image: supply_item_image,
					    total_item_price: total_item_price
					});

				    item_data = `
				        <div class="item supply_checkout_cart_item" data-supply_item_id="${supply_item_id}">
				            <img class="ui item_avatar image" src="<?php echo base_url();?>photos/supply_images/${supply_item_image}">
				            <div class="content">
				                <div class="header supply_cart_item_name">${supply_item_name}</div>
				                <div class="meta">
				                    <span>₱<x class="supply_cart_item_price">${formatted_item_price}</x>
				                    - <x class="supply_cart_item_count">${item_count}</x>
				                    <x class="supply_cart_item_unit">${supply_item_unit}</x></span>
				                </div>
				            </div>
				            <div class="right floated content supply_cart_actions">
				                <i class="ui link red minus circle icon supply_remove_item"></i>
				            </div>
				        </div>`;

				    $('#supply_checkout_cart_content').append(item_data);

				    let cart_item_count = $('#supply_checkout_cart_content .supply_checkout_cart_item').length;

				    if (cart_item_count > 0) {
				    	$('#supply_checkout_code').addClass('hidden');
				    	$('#supply_checkout_code').removeClass('visible');
				    	
				    	$('#supply_checkout_cart_empty_message').addClass('hidden');
				    	$('#supply_checkout_cart_empty_message').removeClass('visible');
				    }
				    else {
				    	$('#supply_checkout_cart_empty_message').addClass('visible');
				    	$('#supply_checkout_cart_empty_message').removeClass('hidden');
				    }
				});


                $('.special.cards .image').dimmer({
				  	on: 'hover'
				});
            }
            else {
        		$(`#supply_items_container`).html('');
                $('#supply_empty_message').removeClass('invisible');
            }
        })
    }

    $('#supply_log_type_dropdown').dropdown('set selected', 'daily');
	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
	    const date_input = document.getElementById('supply_log_date');
	    date_input.value = today;
	});

	$('#supply_log_type_dropdown,#supply_log_date').on('change', function() {
    	load_supply_logs();
	});

    function load_supply_logs() {
		let supply_log_type = $('#supply_log_type').val();
		let supply_log_date = $('#supply_log_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_supply_logs',
            data  : { 
            	supply_log_type:supply_log_type, 
            	supply_log_date:supply_log_date
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.supply_log_table`).addClass('hidden');
    		$(`#supply_${supply_log_type}_log_table`).removeClass('hidden');
            if (response_data != '') {
        		$(`#supply_${supply_log_type}_log`).html('');
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var supply_log_id = value.supply_log_id;
                    var supply_activity_type = value.supply_activity_type;
                    var supply_code = value.supply_code;
                    var supply_activity = value.supply_activity;
                    var timestamp = value.timestamp;

                    let supply_log = `
						<tr>
							<td class="break-text">${supply_activity_type}</td>
							<td class="no-break">${supply_code}</td>
							<td class="break-text">${supply_activity}</td>
							<td class="no-break">${timestamp}</td>
						</tr>
                    `;

            		$(`#supply_${supply_log_type}_log`).append(supply_log);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
            }
            else {
        		$(`#${log_type}_log`).html('');
            }
        })
    }
    $('#supply_logs_activator').on('click', function() {
		$('#supply_logs_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_supply_logs();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

	$('#records_tab').on('click', function() {
		load_supply_restocking_codes();
	});

	$('#supply_restocking_type_dropdown').dropdown('set selected', 'daily');

	function load_supply_restocking_codes() {
		let supply_restocking_report_type = $('#supply_restocking_report_type').val();
		let supply_restocking_report_date = $('#supply_restocking_report_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_supply_restocking_codes',
            data  : { 
            	supply_restocking_report_type:supply_restocking_report_type, 
            	supply_restocking_report_date:supply_restocking_report_date
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.supply_restocking_table`).addClass('hidden');
    		$(`#${supply_restocking_report_type}_supply_restocking_table`).removeClass('hidden');

    		$(`.supply_restocking_tbody`).html('');
            if (response_data != '') {
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var supply_restocking_code = value.supply_restocking_code;
                    var total_item_count = value.total_item_count;
                    var supply_restocking_date = value.supply_restocking_date;
                    var supply_restocking_timestamp = value.supply_restocking_timestamp;

                    let supply_report = `
						<tr class="pointered supply_restocking_view" data-supply_restocking_code="${supply_restocking_code}" data-supply_restocking_date="${supply_restocking_date}">
							<td class="no-break">${supply_restocking_code}</td>
							<td class="no-break">${total_item_count}</td>
							<td class="no-break">${supply_restocking_date}</td>
							<td class="no-break">${supply_restocking_timestamp}</td>
						</tr>
                    `;

                    // alert(`#${supply_restocking_report_type}_supply_restocking`)

            		$(`#${supply_restocking_report_type}_supply_restocking`).append(supply_report);
                })

                $('.supply_restocking_view').on('click', function() {
                	let supply_restocking_code = $(this).data('supply_restocking_code');
                	let supply_restocking_date = $(this).data('supply_restocking_date');
                	$('#supply_transaction_view_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: true,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onShow: function() {
			                	load_supply_restocking(supply_restocking_code, supply_restocking_date);
			                    // load_inactive_clients();
					        }
			            })
			            .modal('show')
			        ;
                });
            }
            else {
    			$(`#${supply_restocking_report_type}_supply_restocking_table`).removeClass('hidden');
    			$(`#${supply_restocking_report_type}_supply_restocking`).html('`<tr><td colspan="6" class="center aligned">No records found</td></tr>`');
            }
        })
    }

    function load_supply_restocking(supply_restocking_code, supply_restocking_date) {
    	let view_activity = `Restocking: <u>${supply_restocking_code}</u>`;
    	let transaction_date = `Date: <u>${supply_restocking_date}</u>`;
	    $('#supply_transaction_void_button').data('supply_transaction_code', supply_restocking_code);
	    $('#supply_transaction_void_button').data('supply_transaction_type', 'restocking');

    	$('#supply_transaction_view_activity').html(view_activity);
    	$('#supply_transaction_view_date').html(transaction_date);

        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_supply_restocking',
            data  : { 
            	supply_restocking_code:supply_restocking_code
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {

        		$(`#supply_transaction_view_container`).html('');
        		let final_total = 0;
                $.each(response_data, function(key, value) {
                    var supply_restocking_id = value.supply_restocking_id;
                    var supply_item_name = value.supply_item_name;
                    var supply_item_image = value.supply_item_image;
                    var supply_item_price = value.supply_item_price;
                    var supply_item_count = value.supply_item_count;
                    var supply_item_unit = value.supply_item_unit;
                    var total_price = supply_item_count*supply_item_price;
                    var display_total_price = parseFloat(supply_item_count*supply_item_price).toFixed(2);

                    let formatted_item_price = supply_item_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    let formatted_total_price = display_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    final_total = final_total + total_price;

                    if (supply_item_count > 1) {
						unit_last = supply_item_unit[supply_item_unit.length-1].toLowerCase();
				        if (
					        unit_last == 's' ||
					        unit_last == 'h' && supply_item_unit.endsWith('sh') ||
					        unit_last == 'h' && supply_item_unit.endsWith('ch') ||
					        unit_last == 'x' ||
					        unit_last == 'z'
					    ) {
					        supply_item_unit = supply_item_unit + 'es';
					    } else {
					        supply_item_unit = supply_item_unit + 's';
					    }
					}


                    let view_data = `
						<tr id="supply_restocking_item${supply_restocking_id}" data-supply_restocking_code="${supply_restocking_code}" data-supply_restocking_date="${supply_restocking_date}">
							<td>${key+1}</td>
							<td class="break-text">
	                			<i class="red pointered x icon  void_supply_restocking_item" data-supply_restocking_id="${supply_restocking_id}" data-supply_item_name="${supply_item_name}"></i>
                                <img src="<?php echo base_url();?>photos/supply_images/${supply_item_image}" class="ui avatar image">
                				<span>${supply_item_name}</span>
							</td>
							<td class="no-break">₱${formatted_item_price}</td>
							<td class="no-break">${supply_item_count} ${supply_item_unit}</td>
							<td class="no-break">₱${formatted_total_price}</td>
						</tr>
                    `;

            		$(`#supply_transaction_view_container`).append(view_data);
                });

                $('.void_supply_restocking_item').on('dblclick', function() {
	            	let supply_restocking_id = $(this).data('supply_restocking_id');
	            	let supply_item_name = $(this).data('supply_item_name');
	            	let confirmed = confirm('Are you sure you want to void this restocking item?');
	            	if (confirmed) {
	            		void_supply_restocking_item(supply_restocking_id, supply_item_name);
	            	}
	            });

                let formatted_final_total = parseFloat(final_total).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#supply_transaction_view_total').html('₱'+formatted_final_total);
            }
            else {
        		$(`#supply_transaction_view_container`).html('');
            }
        })
    }

    function void_supply_restocking_item(supply_restocking_id, supply_item_name) {
	    var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_supply_restocking_item',
	        data  : { 
	            supply_restocking_id: supply_restocking_id
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	alert(`${supply_item_name} was successfully voided from this restocking.`);
	        	let supply_restocking_code = $(`#supply_restocking_item${supply_restocking_id}`).data('supply_restocking_code');
	        	let supply_restocking_date = $(`#supply_restocking_item${supply_restocking_id}`).data('supply_restocking_date');

	        	load_supply_restocking(supply_restocking_code, supply_restocking_date);
            	load_supply_inventory();
	        }
	        else if (response == 'success-null') {
	        	$('#supply_transaction_view_modal').modal('hide');
	        	alert(`${supply_item_name} was successfully voided. All items from this restock has been voided.`);
	        	load_supply_restocking_codes();
            	load_supply_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

	$('#supply_transaction_void_button').on('dblclick', function() {
		let supply_transaction_code = $(this).data('supply_transaction_code');
		let supply_transaction_type = $(this).data('supply_transaction_type');
    	let confirmed = confirm('Are you sure you want to void this transaction and all related items? Once voided, it cannot be restored.');
    	if (confirmed) {
    		if (supply_transaction_type == 'checkout') {
	    		void_supply_checkout(supply_transaction_code);
    		}
    		else if (supply_transaction_type == 'restocking') {
	    		void_supply_restocking(supply_transaction_code);
    		}
    	}
	});

	function void_supply_restocking(supply_restocking_code) {
		var ajax = $.ajax({
	        method: 'POST',
	        url   : '<?php echo base_url();?>i.php/sys_control/void_supply_restocking',
	        data  : { 
	            supply_restocking_code: supply_restocking_code
	        }
	    });

	    var jqxhr = ajax
	    .always(function() {
	        var response = jqxhr.responseText;
	        if (response == 'success') {
	        	$('#supply_transaction_view_modal').modal('hide');
	        	alert(`Restocking with code "${supply_restocking_code}" was successfully voided.`);
	        	load_supply_restocking_codes();
	            load_supply_inventory();
	        }
	        else {
	        	alert(`Voiding failed. Please try again.`);
	        }
	    });
	}

    $('#supply_restocking_type_dropdown,#supply_restocking_report_date').on('change', function() {
    	load_supply_restocking_codes();
	});

	$('#supply_restocking_items_drop, #supply_restock_quantity').on('keydown', function(e) {
	    if (e.key === 'Enter') {
	        $('#supply_restocking_form').submit();
	    }
	});


    $('#supply_logs_activator').on('click', function() {
		$('#supply_logs_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_supply_logs();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});

    $('#supply_checkout_submit').on('dblclick', function(e) {
	    e.preventDefault();

	    if (!supply_cart_array || supply_cart_array.length === 0) {
	        alert('Cart is empty.');
	        return;
	    }

	    let formData = new FormData();
	    formData.append('cart_items', JSON.stringify(supply_cart_array)); // send cart as JSON string

	    $.ajax({
	        url: '<?php echo base_url(); ?>i.php/sys_control/supply_checkout',
	        method: 'POST',
	        data: formData,
	        processData: false,  // important for FormData
	        contentType: false,  // important for FormData
	        success: function (response) {
	            if (response === 'success') {
	                supply_cart_array = []; // clear current cart
			    	$('#supply_checkout_cart_empty_message').addClass('hidden');
			    	$('#supply_checkout_cart_empty_message').removeClass('visible');
			    	$('#supply_checkout_cart_content').html('');
	                alert('Checkout successful!');
	            } 
	            else if (response === 'empty_cart') {
	                alert('Cart is empty. Nothing to checkout.');
	            }
	            else {
	                alert('Checkout failed. Try again.');
	            }
	        },
	        error: function (xhr, status, error) {
	            console.error(xhr.responseText);
	            alert('AJAX error during checkout.');
	        }
	    });
	});


    $('.new_supply_item_activator').on('click', function (event) {
    	toggle_new_supply_capture_button();
        $('#new_supply_item_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onHide: function() {
		            stop_new_supply_camera();
		        },
		        onShow: function(){
		        	start_new_supply_camera();
		        }
            })
            .modal('show')
        ;
    });

    function initialize_supply_item_camera() {
	    const video = $('#new_supply_camera_stream')[0];
	    const canvas = $('#new_supply_captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#new_supply_capture_button');
	    const retake_button = $('#new_supply_retake_button');
	    const file_input = $('#new_supply_image_file')[0];
	    const uploaded_preview = $('#new_supply_uploaded_preview');

	    // --- Setup video element ---
	    $('#new_supply_camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover',
	        'cursor': 'pointer',
	        'pointer-events': 'auto'
	    });

	    // --- Handle camera or upload click ---
	    $('#new_supply_camera_stream, #new_supply_camera_container').off('dblclick').on('dblclick', function () {
	        console.log('Double-click detected — opening file chooser');
	        file_input.click();
	    });

	    // --- File input handling ---
	    file_input.addEventListener('change', function (e) {
	        const file = e.target.files[0];
	        if (!file) return;

	        const reader = new FileReader();
	        reader.onload = function (ev) {
	            // Hide video & canvas, show uploaded preview
	            $('#new_supply_camera_stream, #new_supply_captured_canvas').hide();
	            uploaded_preview.attr('src', ev.target.result).show();

	            // Generate filename
	            const item_name = $('#new_supply_item_name').val().replace(/\s+/g, '_');
	            const timestamp = Date.now();
	            const file_name = `${item_name}_${timestamp}.png`;

	            // Assign to hidden inputs
	            $('#new_supply_item_image').val(ev.target.result);
	            $('#new_supply_item_image_name').val(file_name).removeClass('invisible');

	            capture_button.hide();
	            retake_button.show();
	        };
	        reader.readAsDataURL(file);
	    });

	    // --- Capture from camera ---
	    capture_button.off('click').on('click', function() {
	        const item_name = $('#new_supply_item_name').val().replace(/\s+/g, '_');
	        const timestamp = Date.now();
	        const file_name = `${item_name}_${timestamp}.png`;

	        $('#new_supply_item_image_name').val(file_name).removeClass('invisible');

	        const aspect_w = 5, aspect_h = 4;
	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;
	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#new_supply_camera_stream').hide();
	        $('#new_supply_captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#new_supply_item_image').val(canvas.toDataURL('image/png'));
	    });

	    // --- Retake logic ---
	    retake_button.off('click').on('click', function() {
	        $('#new_supply_captured_canvas, #new_supply_uploaded_preview').hide();
	        $('#new_supply_camera_stream').show();
	        capture_button.show();
	        retake_button.hide();

	        $('#new_supply_item_image').val('');
	        $('#new_supply_item_image_name').val('').addClass('invisible');
	        file_input.value = '';
	    });
	}
	function initialize_supply_item_update_camera() {
	    const video = $('#update_supply_camera_stream')[0];
	    const canvas = $('#update_supply_captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#update_supply_capture_button');
	    const retake_button = $('#update_supply_retake_button');
	    const file_input = $('#update_supply_image_file')[0];
	    const uploaded_preview = $('#update_supply_uploaded_preview');

	    // --- Setup video element ---
	    $('#update_supply_camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover',
	        'cursor': 'pointer',
	        'pointer-events': 'auto'
	    });

	    // --- Handle camera or upload click ---
	    $('#update_supply_camera_stream, #update_supply_camera_container').off('dblclick').on('dblclick', function () {
	        console.log('Double-click detected — opening file chooser');
	        file_input.click();
	    });

	    // --- File input handling ---
	    file_input.addEventListener('change', function (e) {
	        const file = e.target.files[0];
	        if (!file) return;

	        const reader = new FileReader();
	        reader.onload = function (ev) {
	            // Hide video & canvas, show uploaded preview
	            $('#update_supply_camera_stream, #update_supply_captured_canvas').hide();
	            uploaded_preview.attr('src', ev.target.result).show();

	            // Generate filename
	            const item_name = $('#update_supply_item_name').val().replace(/\s+/g, '_');
	            const timestamp = Date.now();
	            const file_name = `${item_name}_${timestamp}.png`;

	            // Assign to hidden inputs
	            $('#update_supply_item_image').val(ev.target.result);
	            $('#update_supply_item_image_name').val(file_name).removeClass('invisible');

	            capture_button.hide();
	            retake_button.show();
	        };
	        reader.readAsDataURL(file);
	    });

	    // --- Capture from camera ---
	    capture_button.off('click').on('click', function() {
	        const item_name = $('#update_supply_item_name').val().replace(/\s+/g, '_');
	        const timestamp = Date.now();
	        const file_name = `${item_name}_${timestamp}.png`;

	        $('#update_supply_item_image_name').val(file_name).removeClass('invisible');

	        const aspect_w = 5, aspect_h = 4;
	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;
	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#update_supply_camera_stream').hide();
	        $('#update_supply_captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#update_supply_item_image').val(canvas.toDataURL('image/png'));
	    });

	    // --- Retake logic ---
	    retake_button.off('click').on('click', function() {
	        $('#update_supply_captured_canvas, #update_supply_uploaded_preview').hide();
	        $('#update_supply_camera_stream').show();
	        capture_button.show();
	        retake_button.hide();

	        $('#update_supply_item_image').val('');
	        $('#update_supply_item_image_name').val('').addClass('invisible');
	        file_input.value = '';
	    });

	    $('#update_supply_item_image_button').on('click', function() {
	    	$('#current_supply_image_field').addClass('invisible');
	    	$('#update_supply_webcam_field').removeClass('invisible');
            start_update_supply_camera();

	    });
	    $('#cancel_supply_image_update').on('click', function() {
	    	$('#current_supply_image_field').removeClass('invisible');
	    	$('#update_supply_webcam_field').addClass('invisible');
            stop_update_supply_camera();
	    });
	}

	function toggle_new_supply_capture_button() {
	    let item_name = $('input[name="new_supply_item_name"]').val();

	    if (item_name !== '') {
	        $('#new_supply_capture_button').show();
	    } else {
	        $('#new_supply_capture_button').hide();
	    }
	}

	$('input[name="new_supply_item_name"]').on('change', toggle_new_supply_capture_button);

	$('#new_supply_item_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();
	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/new_supply_item',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        alert('New Item added successfully.')
	                        $('#new_supply_item_modal').modal('hide');
	                    	load_supply_inventory();
	                    }
	                    else {
	                        alert('Time Profile creation failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            new_supply_item_name: {
	                identifier: 'new_supply_item_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Name.'
	                    }
	                ]
	            },
	            new_supply_item_price: {
	                identifier: 'new_supply_item_price',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Number.'
	                    }
	                ]
	            },
	            new_supply_item_image_name: {
	                identifier: 'new_supply_item_image_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Image.'
	                    }
	                ]
	            },
	            new_supply_item_stock: {
	                identifier: 'new_supply_item_stock',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Num.'
	                    }
	                ]
	            },
	            new_supply_item_unit: {
	                identifier: 'new_supply_item_unit',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a valid Number.'
	                    }
	                ]
	            },
	            new_supply_item_image: {
	                identifier: 'new_supply_item_image',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a valid Image.'
	                    }
	                ]
	            }
	        }
	    })
	;

	$('#update_supply_item_form')
    .form({
        on: 'change',
        inline: false,
        transition: 'fade',
        onSuccess: function(event) {
            event.preventDefault();

            var ajax = $.ajax({
                method: 'POST',
                url   : '<?php echo base_url();?>i.php/sys_control/update_supply_item',
                data  : new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            var jqxhr = ajax
                .always(function() {
                    var response = jqxhr.responseText.trim();
                    if (response == 'success') {
                        alert('Item Updated Successfully.');
                        $('#update_supply_item_modal').modal('hide');
                    	load_supply_inventory();
                    } 
                    else {
                        alert('Update Failed. Please try again.');
                    }
                });
        },
        fields: {
            update_supply_item_name: {
                identifier: 'update_supply_item_name',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter the item name.'
                    }
                ]
            },
            update_supply_item_price: {
                identifier: 'update_supply_item_price',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter a price.'
                    },
                    {
                        type: 'decimal',
                        prompt: 'Price must be a valid number.'
                    }
                ]
            },
            update_supply_item_unit: {
                identifier: 'update_supply_item_unit',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter the unit (e.g. piece, box).'
                    }
                ]
            },
            update_supply_item_stock: {
                identifier: 'update_supply_item_stock',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter current stock.'
                    },
                    {
                        type: 'integer',
                        prompt: 'Stock must be a whole number.'
                    }
                ]
            },
            update_supply_item_low: {
                identifier: 'update_supply_item_low',
                rules: [
                    {
                        type: 'empty',
                        prompt: 'Please enter low stock level.'
                    },
                    {
                        type: 'integer',
                        prompt: 'Low stock level must be a whole number.'
                    }
                ]
            }
        }
    });

    let new_supply_camera_stream = null;
	function start_new_supply_camera() {
		const video = $('#new_supply_camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            new_supply_camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_new_supply_camera() {
	    if (new_supply_camera_stream) {
	        new_supply_camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        new_supply_camera_stream = null;
	    }

	    const video = $('#new_supply_camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}

	let update_supply_camera_stream = null;
	function start_update_supply_camera() {
		const video = $('#update_supply_camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            update_supply_camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_update_supply_camera() {
	    if (update_supply_camera_stream) {
	        update_supply_camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        update_supply_camera_stream = null;
	    }

	    const video = $('#update_supply_camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}





































    function initialize_time_manager_camera() {
		const video = $('#camera_stream')[0];
	    const canvas = $('#captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#capture_button');
	    const retake_button = $('#retake_button');

	    // Force 5:4 aspect ratio on the live video
	    $('#camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover'
	    });

	    // Capture with 5:4 aspect ratio
	    capture_button.on('click', function() {
	        let full_name = $('input[name="full_name"]').val().replace(/\s+/g, '_');
	        let birthdate = $('input[name="birthdate"]').val().replace(/-/g, '_');
	        let file_name = `${full_name}_${birthdate}.png`;
	    
	        $('#profile_image_name').val(file_name);
	        image_name = $('#profile_image_name').val();

	        if (image_name != '') {
		        $('#profile_image_name').removeClass('invisible');
	        }
	        else {
		        $('#profile_image_name').addClass('invisible');
	        }

	        const aspect_w = 5;
	        const aspect_h = 4;

	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;

	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#camera_stream').hide();
	        $('#captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#profile_image').val(canvas.toDataURL('image/png'));
	    });

	    // Retake logic
	    retake_button.on('click', function() {
	        $('#captured_canvas').hide();
	        $('#camera_stream').show();
	        capture_button.show();
	        retake_button.hide();
	        $('#profile_image').val('');
	        $('#profile_image_name').val('');

	        image_name = $('#profile_image_name').val();
	        if (image_name != '') {
		        $('#profile_image_name').removeClass('invisible');
	        }
	        else {
		        $('#profile_image_name').addClass('invisible');
	        }
	    });
	}
	
	function initialize_time_manager_update_camera() {
		const video = $('#update_camera_stream')[0];
	    const canvas = $('#update_captured_canvas')[0];
	    const context = canvas.getContext('2d');
	    const capture_button = $('#update_capture_button');
	    const retake_button = $('#update_retake_button');

	    // Force 5:4 aspect ratio
	    $('#update_camera_stream').css({
	        'aspect-ratio': '5 / 4',
	        'width': '100%',
	        'height': 'auto',
	        'object-fit': 'cover'
	    });

	    // Capture new image
	    capture_button.on('click', function(e) {
	        e.preventDefault();

	        let full_name = $('#update_full_name').val().replace(/\s+/g, '_');
	        let birthdate = $('#update_birthdate').val().replace(/-/g, '_');
	        let file_name = `${full_name}_${birthdate}.png`;
	    
	        $('#update_profile_image_name').val(file_name);

	        const aspect_w = 5;
	        const aspect_h = 4;
	        const vid_w = video.videoWidth;
	        const vid_h = video.videoHeight;

	        let target_w = vid_w;
	        let target_h = Math.floor((vid_w / aspect_w) * aspect_h);

	        if (target_h > vid_h) {
	            target_h = vid_h;
	            target_w = Math.floor((vid_h / aspect_h) * aspect_w);
	        }

	        const offset_x = Math.floor((vid_w - target_w) / 2);
	        const offset_y = Math.floor((vid_h - target_h) / 2);

	        canvas.width = target_w;
	        canvas.height = target_h;
	        context.drawImage(video, offset_x, offset_y, target_w, target_h, 0, 0, target_w, target_h);

	        $('#update_camera_stream').hide();
	        $('#update_captured_canvas').show();
	        capture_button.hide();
	        retake_button.show();

	        $('#update_profile_image').val(canvas.toDataURL('image/png'));
	    });

	    // Retake new image
	    retake_button.on('click', function(e) {
	        e.preventDefault();

	        $('#update_captured_canvas').hide();
	        $('#update_camera_stream').show();
	        capture_button.show();
	        retake_button.hide();
	        $('#update_profile_image').val('');
	        $('#update_profile_image_name').val('');
	    });

	    $('#update_image_button').on('click', function() {
	    	$('#current_image_field').addClass('invisible');
	    	$('#update_webcam_field').removeClass('invisible');
        	start_update_camera();
	    });
	    $('#cancel_image_update').on('click', function() {
	    	$('#current_image_field').removeClass('invisible');
	    	$('#update_webcam_field').addClass('invisible');
	    	stop_update_camera();
	    });
	}

	$('.time_manager_header').on('dblclick', function () {
		let active_label = $(this).html().trim();
		if (active_label == 'Active Playtimes') {
			load_registered_clients();
		}
		else if (active_label == 'Registered Clients') {
			load_archived_clients();
		}
		else if (active_label == 'Archived Clients') {
			load_active_clients();
			$(this).html('Active Playtimes');
		}
	})

	$('#report_type_dropdown,#report_date').on('change', function() {
    	load_tm_reports();
	});
	$('#log_type_dropdown,#log_date').on('change', function() {
    	load_tm_logs();
	});

	$('#report_type_dropdown').dropdown('set selected', 'daily');
	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
	    const date_input = document.getElementById('report_date');

	    date_input.value = today;
	});

	$('#log_type_dropdown').dropdown('set selected', 'daily');
	document.addEventListener('DOMContentLoaded', function () {
	    const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
	    const date_input = document.getElementById('log_date');

	    date_input.value = today;
	});

	function load_tm_reports() {
		let report_type = $('#report_type').val();
		let report_date = $('#report_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_tm_reports',
            data  : { 
            	report_type:report_type, 
            	report_date:report_date
            }
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.tm_report_table`).addClass('hidden');
    		$(`#${report_type}_report_table`).removeClass('hidden');
            if (response_data != '') {
        		$(`#${report_type}_report`).html('');
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var log_id = value.log_id;
                    var client_id = value.client_id;
                    var full_name = value.full_name;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;
                    var total_hours = value.total_hours;
                    var total_minutes = value.total_minutes;
                    var total_rate = value.total_rate;

                    let hour_text;
                    let minute_text;

                    let time = '';
					if (total_hours > 0 && total_minutes > 0) {
					    time = total_hours + ' hour' + (total_hours > 1 ? 's ' : ' ') + total_minutes + ' min' + (total_minutes > 1 ? 's' : '');
					} else if (total_hours > 0) {
					    time = total_hours + ' hour' + (total_hours > 1 ? 's' : '');
					} else if (total_minutes > 0) {
					    time = total_minutes + ' min' + (total_minutes > 1 ? 's' : '');
					} else {
					    time = 'Unlimited';
					}

                    final_rate = Number(total_rate)+Number(final_rate);
	                total_time = `${total_hours} ${hour_text} ${total_minutes} ${minute_text}`

	                formatted_total_rate = Number(total_rate).toLocaleString();
                   
                    let client_log = `
						<tr>
							<td class="no-break">
                                <img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}" class="ui avatar image">
                				<span>${full_name}</span>
							</td>
							<td class="no-break">${time}</td>
							<td class="no-break">₱${formatted_total_rate}.00</td>
						</tr>
                    `;

            		$(`#${report_type}_report`).append(client_log);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
                final_rate = final_rate.toLocaleString();
        		$(`#${report_type}_reports_total_rate`).html(`₱${final_rate}.00`);
            }
            else {
        		$(`#${report_type}_report`).html('');
        		$(`#${report_type}_reports_total_rate`).html(`₱0.00`);
            }
        })
    }
    function load_tm_logs() {
		let log_type = $('#log_type').val();
		let log_date = $('#log_date').val();
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_tm_logs',
            data  : { 
            	log_type:log_type, 
            	log_date:log_date
            }
        });
        var jqxhr = ajax
        .always(function() {
        	$
            var response_data = JSON.parse(jqxhr.responseText);
    		$(`.tm_log_table`).addClass('hidden');
    		$(`#${log_type}_log_table`).removeClass('hidden');
            if (response_data != '') {
        		$(`#${log_type}_log`).html('');
        		let final_rate = 0;
                $.each(response_data, function(key, value) {
                    var log_id = value.log_id;
                    var client_id = value.client_id;
                    var full_name = value.full_name;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;
                    var activity = value.activity;
                    var time_stamp = value.time_stamp;

					const d = new Date(time_stamp);

					const months = [
					  'January','February','March','April','May','June',
					  'July','August','September','October','November','December'
					];

					const month = months[d.getMonth()];
					const day = d.getDate();
					const year = d.getFullYear();

					let hours = d.getHours();
					const minutes = d.getMinutes().toString().padStart(2,'0');
					const ampm = hours >= 12 ? 'PM' : 'AM';
					hours = hours % 12 || 12;

					const formatted = `${month} ${day}, ${year} - ${hours}:${minutes} ${ampm}`;

                    let client_log = `
						<tr>
							<td class="no-break">
                                <img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}" class="ui avatar image">
                				<span>${full_name}</span>
							</td>
							<td class="break-text">${activity}</td>
							<td class="no-break">${formatted}</td>
						</tr>
                    `;

            		$(`#${log_type}_log`).append(client_log);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
            }
            else {
        		$(`#${log_type}_log`).html('');
            }
        })
    }

	$('#tm_reports_activator').on('click', function() {
		$('#time_reports_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_tm_reports();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});
	$('#tm_logs_activator').on('click', function() {
		$('#time_logs_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                	load_tm_logs();
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
	});


	function loading_start() {
		if (!$('#loading_overlay').transition('is visible')) {
			$('#loading_overlay').transition('fade');
			document.body.classList.add('no_scroll');
		}
		else {
			document.body.classList.remove('no_scroll');
		}
	}

	function load_active_clients() {
		$('.time_manager_header').html('Active Playtimes');
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_active_clients'
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
            	$('#times_container').html('');
            	$('#extend_client_drop_menu').html('');
                $('#empty_message').addClass('invisible');
                $.each(response_data, function(key, value) {
                    var client_id = value.client_id;
                    var guardian_name = value.guardian_name;
                    var guardian_contact = value.guardian_contact;
                    var full_name = value.full_name;
                    var gender = value.gender;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;
                    var start_time = value.start_time;
                    var hour = value.total_hours;
                    var minute = value.total_minutes;
                    var price = value.total_price;
	    			var age = get_age(birthdate);

	    			const [hours, minutes] = start_time.split(':').map(Number);
    				const am_pm = hours >= 12 ? 'PM' : 'AM';
    				const formatted_hours = hours % 12 || 12; // convert 0 to 12
    				formatted_start_time = `${formatted_hours}:${String(minutes).padStart(2, '0')} ${am_pm}`;

                    if (gender == 'F') {
                    	gender_color = 'pink';
                    }
                    else if (gender == 'M') {
                    	gender_color = 'blue';
                    }

                    if (age > 4) {
                    	supervision_check = '';
                    }
                    else {
                    	supervision_check = '';
                    	// supervision_check = ' (Needs Adult supervision)';
                    }

	    			if (age > 1) {
	    				age_text = ' years old';
	    			}
	    			else {
	    				age_text = ' year old';
	    			}
	    			var age = age+age_text+supervision_check;
	    			var current_time = get_current_time();
                    
                    let client_card = `
						<div class="ui fluid link card">
						    <div class="blurring dimmable image image-container">
								<div class="ui dimmer">
									<div class="content">
										<div class="ui teal tiny inverted button extend_time" data-client_id="${client_id}" data-full_name="${full_name}" id="${client_id}extend_time">
											Extend Time
										</div>
										<br>
										<br>
										<div class="ui yellow tiny inverted button end_time" data-client_id="${client_id}" data-full_name="${full_name}" id="${client_id}end_time">
											End Time
										</div>
                    					<br>
										<br>
										<div class="ui red tiny inverted button remove_time" data-client_id="${client_id}" data-full_name="${full_name}" data-hour="${hour}" data-minute="${minute}" data-price="${price}" id="${client_id}remove_time">
											Remove
										</div>
									</div>
								</div>
                            	<img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}">
							</div>
						    <div class="content">
						        <a class="header">${full_name}</a>
						        <div class="meta">
						            <span class="ui ${gender_color} tiny circular label">${gender}</span>
						            <span class="ui tag label">${age}</span>
						        </div>
						        <a class="ui tiny header description">
						            <u>${guardian_name}</u>
                					<br>
						            ${guardian_contact}
						        </a>
						    </div>
						    <div class="extra content">
						        <div class="ui small black header">
                    				<div class="right floated content">
							            <i class="clock outline icon"></i><x class="transition" id="${client_id}time"></x>
                    				</div>
                    				<div class="content">
							            <i class="hourglass start icon"></i>${formatted_start_time}
                    				</div>
						            
						        </div>
						    </div>
						</div>
                    `;

                    let client_item = `
                        <div class="item client_option" data-value="${client_id}">
                            <div class="ui avatar image image_container">
                                <img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}" class="center middle aligned flowing_image image bordered">
                            </div>
                            <span>${full_name}</span>
                        </div>
                    `;

                    $('#times_container').append(client_card);
                    
                    
                    $('#extend_client_drop_menu').append(client_item);

                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
					start_countdown(start_time, hour, minute, client_id);
                })
				$('#extend_client_drop')
                    .dropdown({
                        onChange: function() {
                            var input_value = $('#client_id').val();
                            var input_text = $('#new_client_drop').dropdown('get text');
                            $('#new_client_icon').addClass('hidden');
                        }
                    })
                ;
                $('.extend_time').on('dblclick', function() {
                	let client_id = $(this).data('client_id');
                	$('#extend_client_drop').dropdown('set selected', String(client_id));

			    	load_inactive_clients();
			        load_time_rates();
			        $('#extend_time_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: false,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onShow: function() {
			                    // load_inactive_clients();
					        }
			            })
			            .modal('show')
			        ;
			    });
			    $('.end_time').on('dblclick', function() {
			    	var client_id = $(this).data('client_id');
			    	var full_name = $(this).data('full_name');

			    	var ajax = $.ajax({
		                method: 'POST',
		                url   : '<?php echo base_url();?>i.php/sys_control/end_client_time',
		                data  : { client_id:client_id }
		            });
		            var jqxhr = ajax
		                .always(function() {
		                    var response = jqxhr.responseText;
		                    if (response == 'success') {
		                        alert(`${full_name} time Ended`);
		                        load_active_clients();
		                    }
		                    else {
		                        alert('An error occurred. Please try again.')
		                    }
		                })
		            ;
			    });
			    $('.remove_time').on('dblclick', function() {
			    	var client_id = $(this).data('client_id');
			    	var full_name = $(this).data('full_name');
			    	var hour = $(this).data('hour');
			    	var minute = $(this).data('minute');
			    	var price = $(this).data('price');

			    	var ajax = $.ajax({
		                method: 'POST',
		                url   : '<?php echo base_url();?>i.php/sys_control/remove_client_time',
		                data  : { 
		                	client_id:client_id,
		                	hour:hour,
		                	minute:minute,
		                	price:price
		                }
		            });
		            var jqxhr = ajax
		                .always(function() {
		                    var response = jqxhr.responseText;
		                    if (response == 'success') {
		                        alert(`${full_name} Time Profile Removed`);
		                        load_active_clients();
		                    }
		                    else {
		                        alert('An error occurred. Please try again.')
		                    }
		                })
		            ;
			    });
            }
            else {
            	$('#times_container').html('');
                $('#empty_message').removeClass('invisible');
            }
        })
    }
    function load_registered_clients() {
		$('.time_manager_header').html('Registered Clients');
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_registered_clients'
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
            	$('#times_container').html('');
                $('#empty_message').addClass('invisible');
                $.each(response_data, function(key, value) {
                    var client_id = value.client_id;
                    var guardian_name = value.guardian_name;
                    var guardian_contact = value.guardian_contact;
                    var full_name = value.full_name;
                    var gender = value.gender;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;
	    			var age = get_age(birthdate);

                    if (gender == 'F') {
                    	gender_color = 'pink';
                    }
                    else if (gender == 'M') {
                    	gender_color = 'blue';
                    }

                    if (age > 4) {
                    	supervision_check = '';
                    }
                    else {
                    	supervision_check = ' (Needs Adult supervision)';
                    }

	    			if (age > 1) {
	    				age_text = ' years old';
	    			}
	    			else {
	    				age_text = ' year old';
	    			}
	    			var age = age+age_text+supervision_check;
                    
                    let client_card = `
						<div class="ui fluid link card">
						    <div class="blurring dimmable image image-container">
								<div class="ui dimmer">
									<div class="content">
										<div class="ui teal tiny inverted button manage_client" data-client_id="${client_id}" data-guardian_name="${guardian_name}" data-guardian_contact="${guardian_contact}" data-full_name="${full_name}" data-gender="${gender}" data-birthdate="${birthdate}" data-profile_image="${profile_image}" id="${client_id}manage">
											Manage Client Details
										</div>
										<br>
										<br>
										<div class="ui orange tiny inverted button archive_client" data-client_id="${client_id}" data-full_name="${full_name}" id="${client_id}archive">
											Archive Client
										</div>
									</div>
								</div>
                            	<img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}">
							</div>
						    <div class="content">
						        <a class="header">${full_name}</a>
						        <div class="meta">
						            <span class="ui ${gender_color} tiny circular label">${gender}</span>
						            <span class="ui tag label">${age}</span>
						        </div>
						        <a class="ui tiny header description">
						            <u>${guardian_name}</u>
                					<br>
						            ${guardian_contact}
						        </a>
						    </div>
						</div>
                    `;

                    $('#times_container').append(client_card);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
                function open_update_modal(data) {
			        $('#update_client_id').val(data.client_id);
			        $('#update_guardian_name').val(data.guardian_name);
			        $('#update_guardian_contact').val(data.guardian_contact);
			        $('#update_full_name').val(data.full_name);
                	$('#update_gender_drop').dropdown('set selected', String(data.gender));
			        $('#update_birthdate').val(data.birthdate);
			        $('#current_profile_image').attr('src', data.profile_image_url);

			        $('#profile_update_modal').modal('show');
			    }
                $('.manage_client').on('dblclick', function() {
			    	let client_id = $(this).data('client_id');
			    	let guardian_name = $(this).data('guardian_name');
			    	let guardian_contact = $(this).data('guardian_contact');
			    	let full_name = $(this).data('full_name');
			    	let gender = $(this).data('gender');
			    	let birthdate = $(this).data('birthdate');
			    	let profile_image = $(this).data('profile_image');

			    	open_update_modal({
				        client_id: client_id,
				        guardian_name: guardian_name,
				        guardian_contact: guardian_contact,
				        full_name: full_name,
				        gender: gender,
				        birthdate: birthdate,
				        profile_image_url: `<?php echo base_url();?>photos/profile_pictures/${profile_image}`
				    });

			        $('#profile_update_modal')
			            .modal({
			                useFlex: true,
			                allowMultiple: false,
			                autofocus: false,
			                blurring: true,
			                closable: false,
			                onHide: function() {
			                	stop_update_camera();
			                	// start_update_camera();
			                    // load_inactive_clients();
					        }
			            })
			            .modal('show')
			        ;
			    });
			    $('.archive_client').on('dblclick', function() {
			    	var client_id = $(this).data('client_id');
			    	var full_name = $(this).data('full_name');

			    	var ajax = $.ajax({
		                method: 'POST',
		                url   : '<?php echo base_url();?>i.php/sys_control/archive_client',
		                data  : { client_id:client_id }
		            });
		            var jqxhr = ajax
		                .always(function() {
		                    var response = jqxhr.responseText;
		                    if (response == 'success') {
		                        alert(`${full_name} Profile Archived`);
		                        load_registered_clients();
		                    }
		                    else {
		                        alert('An error occurred. Please try again.')
		                    }
		                })
		            ;
			    });
            }
            else {
            	$('#times_container').html('');
                $('#empty_message').removeClass('invisible');
            }
        })
    }
    function load_archived_clients() {
		$('.time_manager_header').html('Archived Clients');
        var ajax = $.ajax({
            method: 'POST',
            url   : '<?php echo base_url();?>i.php/sys_control/load_archived_clients'
        });
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
            	$('#times_container').html('');
                $('#empty_message').addClass('invisible');
                $.each(response_data, function(key, value) {
                    var client_id = value.client_id;
                    var guardian_name = value.guardian_name;
                    var guardian_contact = value.guardian_contact;
                    var full_name = value.full_name;
                    var gender = value.gender;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;
	    			var age = get_age(birthdate);

                    if (gender == 'F') {
                    	gender_color = 'pink';
                    }
                    else if (gender == 'M') {
                    	gender_color = 'blue';
                    }

                    if (age > 4) {
                    	supervision_check = '';
                    }
                    else {
                    	supervision_check = ' (Needs Adult supervision)';
                    }

	    			if (age > 1) {
	    				age_text = ' years old';
	    			}
	    			else {
	    				age_text = ' year old';
	    			}
	    			var age = age+age_text+supervision_check;
                    
                    let client_card = `
						<div class="ui fluid link card">
						    <div class="blurring dimmable image image-container">
								<div class="ui dimmer">
									<div class="content">
										<div class="ui teal tiny inverted button unarchive_client" data-client_id="${client_id}" data-full_name="${full_name}" id="${client_id}archive">
											Unarchive Client
										</div>
                    					<br>
										<br>
										<div class="ui red tiny inverted button delete_client" data-client_id="${client_id}" data-full_name="${full_name}" id="${client_id}delete">
											Delete Profile
										</div>
									</div>
								</div>
                            	<img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}">
							</div>
						    <div class="content">
						        <a class="header">${full_name}</a>
						        <div class="meta">
						            <span class="ui ${gender_color} tiny circular label">${gender}</span>
						            <span class="ui tag label">${age}</span>
						        </div>
						        <a class="ui tiny header description">
						            <u>${guardian_name}</u>
                					<br>
						            ${guardian_contact}
						        </a>
						    </div>
						</div>
                    `;

                    $('#times_container').append(client_card);
                    $('.special.cards .image').dimmer({
					  	on: 'hover'
					});
                })
			    $('.unarchive_client').on('dblclick', function() {
			    	var client_id = $(this).data('client_id');
			    	var full_name = $(this).data('full_name');

			    	var ajax = $.ajax({
		                method: 'POST',
		                url   : '<?php echo base_url();?>i.php/sys_control/unarchive_client',
		                data  : { client_id:client_id }
		            });
		            var jqxhr = ajax
		                .always(function() {
		                    var response = jqxhr.responseText;
		                    if (response == 'success') {
		                        alert(`${full_name} Profile Restored`);
		                        load_archived_clients();
		                    }
		                    else {
		                        alert('An error occurred. Please try again.')
		                    }
		                })
		            ;
			    });
			    $('.delete_client').on('dblclick', function() {
			    	var client_id = $(this).data('client_id');
			    	var full_name = $(this).data('full_name');

			    	var ajax = $.ajax({
		                method: 'POST',
		                url   : '<?php echo base_url();?>i.php/sys_control/delete_client',
		                data  : { client_id:client_id }
		            });
		            var jqxhr = ajax
		                .always(function() {
		                    var response = jqxhr.responseText;
		                    if (response == 'success') {
		                        alert(`${full_name} Profile Deleted`);
		                        load_archived_clients();
		                    }
		                    else {
		                        alert('An error occurred. Please try again.')
		                    }
		                })
		            ;
			    });
            }
            else {
            	$('#times_container').html('');
                $('#empty_message').removeClass('invisible');
            }
        })
    }

    // Store timers globally by client_id
	const countdown_timers = {};

	function start_countdown(start_time, hour, minute, client_id) {
	    // Clear any existing timer for this client
	    if (countdown_timers[client_id]) {
	        clearInterval(countdown_timers[client_id]);
	        delete countdown_timers[client_id];
	    }

	    hour = parseInt(hour, 10) || 0;
	    minute = parseInt(minute, 10) || 0;

	    if (hour === 0 && minute === 0) {
	        $(`#${client_id}time`).text('Unlimited');
	        $(`#${client_id}extend_time`).addClass('invisible');
	        return;
	    } else {
	        $(`#${client_id}extend_time`).removeClass('invisible');
	    }

	    const [h, m, s] = start_time.split(':').map(Number);
	    if (isNaN(h) || isNaN(m) || isNaN(s)) {
	        console.error('Invalid start_time format');
	        return;
	    }

	    const start = new Date();
	    start.setHours(h, m, s, 0);

	    const end = new Date(start.getTime());
	    end.setHours(end.getHours() + hour);
	    end.setMinutes(end.getMinutes() + minute);

	    function update_countdown() {
	        const now = new Date();
	        let remaining_ms = end - now;

	        if (remaining_ms <= 0) {
	            const timer_element = $(`#${client_id}time`);
	            timer_element.text('00:00:00');

	            const card_element = timer_element.closest('.card');
	            const sound_effect = new Audio('<?php echo base_url(); ?>audio/coin.mp3');
	            let is_paused = false;
	            let bounce_timeout;

	            function bounce_with_sound() {
	                if (is_paused) return;

	                const animation_duration = 2000;
	                const sound_delay = animation_duration / 2.5;

	                card_element.transition({
	                    animation: 'bounce',
	                    duration: `${animation_duration}ms`,
	                    onStart: () => {
	                        setTimeout(() => {
	                            if (!is_paused) {
	                                sound_effect.currentTime = 0;
	                                sound_effect.play();
	                            }
	                        }, sound_delay);

	                        bounce_timeout = setTimeout(bounce_with_sound, animation_duration);
	                    }
	                });
	            }

	            bounce_with_sound();

	            card_element.on('mouseenter', () => {
	                is_paused = true;
	                card_element.transition('stop');
	                clearTimeout(bounce_timeout);
	            });

	            card_element.on('mouseleave', () => {
	                if (!is_paused) return;
	                is_paused = false;
	                bounce_with_sound();
	            });

	            clearInterval(countdown_timers[client_id]);
	            delete countdown_timers[client_id];
	            return;
	        }

	        const remaining_h = Math.floor(remaining_ms / (1000 * 60 * 60));
	        const remaining_m = Math.floor((remaining_ms % (1000 * 60 * 60)) / (1000 * 60));
	        const remaining_s = Math.floor((remaining_ms % (1000 * 60)) / 1000);

	        $(`#${client_id}time`).text(
	            `${String(remaining_h).padStart(2, '0')}:${String(remaining_m).padStart(2, '0')}:${String(remaining_s).padStart(2, '0')}`
	        );
	    }

	    update_countdown();
	    countdown_timers[client_id] = setInterval(update_countdown, 1000);
	}


    function get_current_time() {
	    const now = new Date();
	    const hours = String(now.getHours()).padStart(2, '0');
	    const minutes = String(now.getMinutes()).padStart(2, '0');
	    const seconds = String(now.getSeconds()).padStart(2, '0');

	    return hours + ':' + minutes + ':' + seconds; // HH:MM:SS format
	}

    function get_age(birth_date_str) {
	    const birth_date = new Date(birth_date_str);
	    const today = new Date();

	    let age = today.getFullYear() - birth_date.getFullYear();
	    const month_diff = today.getMonth() - birth_date.getMonth();
	    const day_diff = today.getDate() - birth_date.getDate();

	    if (month_diff < 0 || (month_diff === 0 && day_diff < 0)) {
	        age--;
	    }

	    return age;
	}

	// $('input[name="birthdate"]').on('change', function() {
	//     const birth_date_str = $(this).val(); // format: YYYY-MM-DD
	//     $('#age_display').text(age); // assuming you have a span/div with this ID
	// });


	function format_name_input(input_element) {
	    let cleaned_value = $(input_element).val().replace(/[^a-zA-Z.\s]/g, '');
	    $(input_element).val(cleaned_value);
	}
	$('input[name="full_name"], input[name="guardian_name"]').on('input', function () {
	    format_name_input(this);
	});
	$('input[name="full_name"], input[name="guardian_name"]').on('input', function () {
	    format_name_input(this);
	}); 


	function format_mobile_number(input_element) {
	    let raw_value = $(input_element).val().replace(/\D/g, ''); // remove all non-digits

	    // Limit to 11 digits max (09XXXXXXXXX)
	    raw_value = raw_value.substring(0, 11);

	    // Apply formatting 09**-***-****
	    let formatted_value = raw_value;
	    if (raw_value.length > 4 && raw_value.length <= 7) {
	        formatted_value = raw_value.substring(0, 4) + '-' + raw_value.substring(4);
	    } else if (raw_value.length > 7) {
	        formatted_value = raw_value.substring(0, 4) + '-' + raw_value.substring(4, 7) + '-' + raw_value.substring(7);
	    }

	    $(input_element).val(formatted_value);
	}

	// Usage: attach to your textbox
	$('input[name="guardian_contact"]').on('input', function () {
	    format_mobile_number(this);
	});
	
	function toggle_capture_button() {
	    let guardian_name = $('input[name="guardian_name"]').val();
	    let guardian_contact = $('input[name="guardian_contact"]').val();
	    let full_name = $('input[name="full_name"]').val();
	    let gender = $('input[name="gender"]').val();
	    let birthdate = $('input[name="birthdate"]').val();

	    if (guardian_name !== '' && guardian_contact !== '' && full_name !== '' && gender !== '' && birthdate !== '') {
	        $('#capture_button').show();
	    } else {
	        $('#capture_button').hide();
	    }
	}
	$('input[name="guardian_name"], input[name="guardian_contact"], input[name="full_name"], input[name="birthdate"]').on('input change', toggle_capture_button);
	$('input[name="gender"]').on('change', toggle_capture_button);

	

	$('#signup_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();

	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/save_child_profile',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        alert('Client Registration Successful.')
	                        reset_signup_form();
	                        load_inactive_clients();
	                        load_registered_clients();
	                        $('#profile_modal').modal('hide');
	                    }
	                    else {
	                        alert('Client Registration Failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            guardian_name: {
	                identifier: 'guardian_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            guardian_contact: {
	                identifier: 'guardian_contact',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            full_name: {
	                identifier: 'full_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            gender: {
	                identifier: 'gender',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select Passengers.'
	                    }
	                ]
	            },
	            birthdate: {
	                identifier: 'birthdate',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a Purpose.'
	                    }
	                ]
	            },
	            profile_image: {
	                identifier: 'profile_image',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a vehicle.'
	                    }
	                ]
	            }
	        }
	    })
	;
	$('#update_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();

	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/update_child_profile',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        alert('Registration Update Successful.')
	                        reset_signup_form();
	                        load_registered_clients();
	                    }
	                    else {
	                        alert('Registration Update Failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            update_guardian_name: {
	                identifier: 'update_guardian_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            update_guardian_contact: {
	                identifier: 'update_guardian_contact',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            update_full_name: {
	                identifier: 'update_full_name',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            update_gender: {
	                identifier: 'update_gender',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select Passengers.'
	                    }
	                ]
	            },
	            update_birthdate: {
	                identifier: 'update_birthdate',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please enter a Purpose.'
	                    }
	                ]
	            }
	        }
	    })
	;

	$('#new_client_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();
	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/new_active_client',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        reset_new_client_form();
	                        load_active_clients();
	                        load_inactive_clients();
	                        alert('New Time Profile added successfully.')
	                        $('#new_client_modal').modal('hide');
	                    }
	                    else {
	                        alert('Time Profile creation failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            client_id: {
	                identifier: 'client_id',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            rate_id: {
	                identifier: 'rate_id',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            }
	        }
	    })
	;
	$('#extend_time_form')
	    .form({
	        on: 'change',
	        inline: false,
	        transition: 'fade',
	        onSuccess: function(event) {
	            event.preventDefault();

	            var ajax = $.ajax({
	                method: 'POST',
	                url   : '<?php echo base_url();?>i.php/sys_control/extend_client_time',
	                data  : new FormData(this),
	                contentType: false,
	                cache: false,
	                processData: false
	            });
	            var jqxhr = ajax
	                .always(function() {
	                    var response = jqxhr.responseText;
	                    if (response == 'success') {
	                        reset_extend_time_form();
	                        load_active_clients();
	                        load_inactive_clients();
	                        alert('Time Profile extended successfully.')
	                    }
	                    else {
	                        alert('Time Profile extension failed. Please try again.')
	                    }
	                })
	            ;
	        },
	        fields: {
	            client_id: {
	                identifier: 'client_id',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            },
	            rate_id: {
	                identifier: 'rate_id',
	                rules: [
	                    {
	                        type: 'empty',
	                        prompt: 'Please select a Requestor.'
	                    }
	                ]
	            }
	        }
	    })
	;

	function reset_new_client_form() {
	    const form = $('#new_client_form');

	    form.find('input[type="hidden"]').val('');
	    form.find('input[type="text"], input[type="number"], input[type="email"], textarea').val('');

	    $('#new_client_drop').dropdown('clear');
	    $('#time_drop').dropdown('clear');

	    $('#new_client_icon').html('<i class="user icon"></i>&nbsp;&nbsp;');
	    $('#time_icon').html('<i class="clock icon"></i>&nbsp;&nbsp;');
	}
	function reset_extend_time_form() {
	    const form = $('#extend_time_form');

	    form.find('input[type="hidden"]').val('');
	    form.find('input[type="text"], input[type="number"], input[type="email"], textarea').val('');

	    $('#extend_time_drop').dropdown('clear');
	    $('#time_drop').dropdown('clear');

	    $('#extend_time_icon').html('<i class="user icon"></i>&nbsp;&nbsp;');
	}
	
	function load_inactive_clients() {
        var ajax = $.ajax("<?php echo base_url();?>i.php/sys_control/load_inactive_clients");
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            $('#new_client_drop_menu').html('');
            if (response_data != '') {
                $.each(response_data, function(key, value) {
                    var client_id = value.client_id;
                    var guardian_name = value.guardian_name.UCwords();
                    var guardian_contact = value.guardian_contact;
                    var full_name = value.full_name.UCwords();
                    var gender = value.gender;
                    var birthdate = value.birthdate;
                    var profile_image = value.profile_image;

                    let client_item = `
                        <div class="item client_option" data-value="${client_id}">
                            <div class="ui avatar image image_container">
                                <img src="<?php echo base_url();?>photos/profile_pictures/${profile_image}" class="center middle aligned flowing_image image bordered">
                            </div>
                            <span>${full_name}</span>
                        </div>
                    `;
                    
                    $('#new_client_drop_menu').append(client_item);
                })
                $('#new_client_drop')
                    .dropdown({
                        onChange: function() {
                            var input_value = $('#client_id').val();
                            var input_text = $('#new_client_drop').dropdown('get text');
                            $('#new_client_icon').addClass('hidden');
                        }
                    })
                ;
            }
        })
    }
    function load_time_rates() {
        var ajax = $.ajax("<?php echo base_url();?>i.php/sys_control/load_time_rates");
        var jqxhr = ajax
        .always(function() {
            var response_data = JSON.parse(jqxhr.responseText);
            if (response_data != '') {
                $('#time_drop_menu').html('');
                $('#extend_time_drop_menu').html('');
                $.each(response_data, function(key, value) {
                    var rate_id = value.rate_id;
                    var hour = value.hour;
                    var minute = value.minute;
                    var price = value.price;

                    if (hour > 1) {
                    	hour = `${hour} hours`
                    }
                    else if (hour == 0) {
                    	hour = '';
                    }
                    else {
                    	hour = `${hour} hour`
                    }

                    if (minute > 1) {
                    	minute = `${minute} minutes`
                    }
                    else if (minute == 0) {
                    	minute = '';
                    }
                    else {
                    	minute = `${minute} minute`
                    }

                    if (hour == 0 && minute == 0) {
                    	time = 'Unlimited';
                    }
                    else {
    	                time = `${hour} ${minute}`
                    }


                    let client_item = `
                        <div class="item time_options" data-value="${rate_id}" data-text="${time}-₱${price}">
                        	<div class="right floated content">
	                            <span>₱${price}</span>
                        	</div>
                        	<div class="content">
	                            <span>${time}</span>
                        	</div>
                        </div>
                    `;
                    
                    $('#time_drop_menu').append(client_item);
                    $('#extend_time_drop_menu').append(client_item);
                })
                $('#time_drop')
                    .dropdown({
                        onChange: function() {
                        }
                    })
                ;
                $('#extend_time_drop')
                    .dropdown({
                        onChange: function() {
                        }
                    })
                ;
            }
        })
    }

	function reset_signup_form() {
	    // Reset form fields
	    $('#signup_form')[0].reset();

	    // Reset Semantic UI dropdown
	    $('#gender_drop').dropdown('clear');

	    // Clear the hidden base64 image field
	    $('#profile_image').val('');

	    // Reset preview: hide captured canvas, show live stream
	    $('#captured_canvas').hide();
	    $('#camera_stream').show();
	}
	let camera_stream = null;
	function start_camera() {
		const video = $('#camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_camera() {
	    if (camera_stream) {
	        camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        camera_stream = null;
	    }

	    const video = $('#camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}

	let update_camera_stream = null;
	function start_update_camera() {
		const video = $('#update_camera_stream')[0];

	    // Start webcam
	    navigator.mediaDevices.getUserMedia({ video: true })
	        .then(function(stream) {
	            update_camera_stream = stream;
	            video.srcObject = stream;
	        })
	        .catch(function(err) {
	            console.error("Camera access denied:", err);
	            alert('Camera not accessible. Please allow permission or use HTTPS.');
	        })
	    ;
	}
	function stop_update_camera() {
	    if (update_camera_stream) {
	        update_camera_stream.getTracks().forEach(track => {
	            track.stop();
	        });
	        update_camera_stream = null;
	    }

	    const video = $('#update_camera_stream')[0];
	    if (video) {
	        video.srcObject = null;
	        video.pause();
	        video.removeAttribute('src');
	        video.load();
	    }
	}

	$('.new_profile_activator').on('click', function (event) {
        toggle_capture_button();
        reset_signup_form();
        $('#profile_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onHide: function() {
		            stop_camera();
		        },
		        onShow: function(){
		        	start_camera();
		        }
            })
            .modal('show')
        ;
    });
    $('.new_client_activator').on('click', function (event) {
        // reset_new_client_form();
        load_inactive_clients();
        load_time_rates();
        $('#new_client_modal')
            .modal({
                useFlex: true,
                allowMultiple: false,
                autofocus: false,
                blurring: true,
                closable: false,
                onShow: function() {
                    // load_inactive_clients();
		        }
            })
            .modal('show')
        ;
    });
	$('.floater-button')
        .dropdown({
            on: 'click',
            transition : 'fade up',
            duration   : 400,
            delay : {
                hide   : 100,
                show   : 100,
                search : 50,
                touch  : 50
            }
        })
    ;
    $('.ui.sortable.table th').on('click', function() {
		const table = $(this).parents('table');
		const rows = table.find('tbody > tr').toArray();
		const index = $(this).index();
		const ascending = !$(this).hasClass('sorted ascending');

		// Clear previous sort indicators
		table.find('th').removeClass('sorted ascending descending');

		// Sort rows
		rows.sort((a, b) => {
		const A = $(a).children('td').eq(index).text().toUpperCase();
		const B = $(b).children('td').eq(index).text().toUpperCase();
		if (A < B) return ascending ? -1 : 1;
		if (A > B) return ascending ? 1 : -1;
		return 0;
		});

		// Re-append sorted rows
		$.each(rows, (_, row) => table.children('tbody').append(row));

		// Add class for icon direction
		$(this).addClass(`sorted ${ascending ? 'ascending' : 'descending'}`);
	});

    $('#report_type_dropdown')
        .dropdown({
            onChange: function() {
                // var input_value = $('#client_id').val();
                // var input_text = $('#new_client_drop').dropdown('get text');
                // $('#new_client_icon').addClass('hidden');
            }
        })
    ;
    $('#gender_drop, #update_gender_drop')
	  	.dropdown()
	;
    $('.tabular.menu .item')
	  .tab()
	;
</script>
</html>