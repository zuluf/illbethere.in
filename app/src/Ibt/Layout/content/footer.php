			</div>
		</div>
		<div id="footer">
			<?php \Ibt\Events::fire( 'footer' ); ?>
			<!-- <span class="licence">
				This product includes GeoLite data<br/>created by MaxMind available from <a href="http://www.maxmind.com" target="_blank">http://www.maxmind.com</a>.
			</span> -->
		</div>

		<?php \Ibt\Events::fire( 'scripts' ); ?>

		<div class="preload">
			<span class="logo-green-preload"></span>
		</div>
	</body>
</html>