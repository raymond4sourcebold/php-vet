
	<!-- Here is the new main navigation -->
	<div class="menu" style='float: left;'>
		<ul>
			<li><a href="/search"><?php echo lang_home; ?></a></li>
			<li><a href="javascript: void(0)"><?php echo lang_client; ?><!--[if IE 7]><!--></a><!--<![endif]-->
				<!--[if lte IE 6]><table><tr><td><![endif]-->
				<ul>
				<li><a href="/clients/create" title="Create client"><?php echo lang_create_client; ?></a></li>
				<li><a href="/clients" title="Find client"><?php echo lang_find_client; ?></a></li>
				</ul>
				<!--[if lte IE 6]></td></tr></table></a><![endif]-->
			</li>
			<li><a href="/search"><?php echo lang_follow_up; ?></a></li>
			<li><a href="javascript: void(0)"><?php echo lang_automate; ?><!--[if IE 7]><!--></a><!--<![endif]-->
				<!--[if lte IE 6]><table><tr><td><![endif]-->
				<ul>
				<li><a href="/commplan" title="Comm plan"><?php echo lang_comm_plan; ?></a></li>
				<li><a href="/procgrid" title="Library autoproc"><?php echo lang_library_autoproc; ?></a></li>
				<li><a href="/procedures/create" title="Create autoproc"><?php echo lang_create_autoproc; ?></a></li>
				<li><a href="/messages" title="Library message"><?php echo lang_library_message; ?></a></li>
				<li><a href="/messages/create" title="Create message"><?php echo lang_create_message; ?></a></li>
				
				<li><a href="/criteria" title="Library criteria">.<?php echo lang_criteria; ?></a></li>
				<li><a href="/species" title="Species">.<?php echo lang_species; ?></a></li>
				<li><a href="/genders" title="Genders">.<?php echo lang_genders; ?></a></li>
				<li><a href="/testuploadini" title="Upload client file">.<?php echo lang_upload_clientfile; ?></a></li>
				</ul>
				<!--[if lte IE 6]></td></tr></table></a><![endif]-->
			</li>
		</ul>
	</div>
	<!--<div style='float: left;'><a class='info' href="/procedures"><img src='/images/logout.gif' /><span style='width:100px;'>Logout</span></a>-->
	<div style='float: left;'><a href="/logout" class="tooltip" title="Logout"><img src='/images/logout.gif' /></a>
	</div>
	<div style='clear: both;'></div>
	<!-- Above: new main navigation -->
