<h3>
	<span id="floatright">
		<b><?php echo $spText['common']['lang']?>:</b> 
		<select name="lang_code" id="lang_code" class="lang_code" onchange="doLoadUrl('lang_code', '<?php echo $redirectUrl?>')">
			<?php
			foreach ($langList as $langInfo) {
				$selected = ($langInfo['lang_code'] == $_SESSION['lang_code']) ? "selected='selected'" : "";
				?>			
				<option value="<?php echo $langInfo['lang_code']?>" <?php echo $selected?>><?php echo $langInfo['lang_show']?></option>
				<?php
			}
			?>
		</select>
		<?php
		$menuInfo = getCustomizerMenu("top");
		if (!empty($menuInfo['item_list'])) {
			$linkStyle = !empty($menuInfo['bg_color']) ? "background-color: " . $menuInfo['bg_color'] : ""; 
			$linkStyle .= !empty($menuInfo['font_color']) ? ";color: " . $menuInfo['font_color'] : "";
			
			// loop through menu items
			foreach ($menuInfo['item_list'] as $menuItem) {
				$linkTarget = ($menuItem['window_target'] == 'new_tab') ? "_blank" : "";
				?>
				<a href="<?php echo $menuItem['url']?>" target="<?php echo $linkTarget?>" style="<?php echo $linkStyle?>">
					<?php echo $menuItem['label']?>
				</a> 
				<span class="pipe">|</span>
				<?php
			}
			
		} else {
			?> 
			<a href="<?php echo !empty($custSiteInfo['contact_url']) ? $custSiteInfo['contact_url'] : SP_CONTACT_LINK?>" target="_blank" title="Contact Seo Panel"><?php echo $spText['common']['contact']?></a> <span class="pipe">|</span>
			<a href="<?php echo !empty($custSiteInfo['help_url']) ? $custSiteInfo['help_url'] : SP_HELP_LINK?>" target="_blank" title="Seo Panel Help Guide"><?php echo $spText['common']['help']?></a> <span class="pipe">|</span>
			<a href="<?php echo !empty($custSiteInfo['forum_url']) ? $custSiteInfo['forum_url'] : SP_FORUM_LINK?>" target="_blank" title="Seo Panel Forum"><?php echo $spText['common']['forum']?></a> <span class="pipe">|</span>
			<?php
		}
		
		$userInfo = @Session::readSession('userInfo');
		if(empty($userInfo['userId'])){	
		?> 			
			<a href="<?php echo SP_WEBPATH?>/login.php"><?php echo $spText['common']['signin']?></a>
		<?php }else{ ?>
			<a href="admin-panel.php?sec=myprofile"><?php echo $spText['common']['Profile']?></a> <span class="pipe">|</span>
			<a href="<?php echo SP_WEBPATH?>/login.php?sec=logout"><?php echo $spText['common']['Sign out']?></a>
		<?php }?>
	</span>
</h3>
