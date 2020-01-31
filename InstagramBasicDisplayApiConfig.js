/**
 * Instagram Basic Display API Config JS
 *
 * @copyright 2020 NB Communication Ltd
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 *
 */

var InstagramBasicDisplayApiConfig = {

	init: function() {
		this.copyAuthorizationCodeUri();
		this.toggleClientSecret();
	},

	iconWidth: 40,

	copyAuthorizationCodeUri: function() {

		var $input = $("#Inputfield_authorizationCodeUri");
		if(!$input.length) return;

		var id = "copy-authorization-code-uri";
		this.setInputWidth($input);
		$input.after(this.renderButton(this.renderIcon("files-o", "Copy to Clipboard"), id));

		$("#" + id).on("click", function(e) {

			e.preventDefault();

			$input.select();
			document.execCommand("copy");

			var $button = $(this);
			var lbl = $button.html();

			$button.html(InstagramBasicDisplayApiConfig.renderIcon("check", "Copied to Clipboard"));
			setTimeout(function() {
				$button.html(lbl);
			}, 2048);
		});
	},

	renderButton: function(label, id, cls) {
		if(cls === void 0) cls = "";
		return "<a " + [
			"href='#'",
			"id='" + id + "'",
			"class='" + cls + "'",
			"style='display:inline-block;width:" + this.iconWidth + "px;text-align:center;'"
		].join(" ") + ">" + label + "</a>";
	},

	renderIcon: function(icon, title) {
		return "<i class='fa fa-" + icon + "' title='" + title + "' aria-hidden='true'></i>";
	},

	setInputWidth: function($input) {
		$input.css("width", "calc(100% - " + this.iconWidth + "px)");
	},

	toggleClientSecret: function() {

		var $input = $("#Inputfield_clientSecret");
		if(!$input.length) return;

		var id = "toggle-client-secret";
		var idShow = id + "-show";
		var lbl = [this.renderIcon("eye", "Show"), this.renderIcon("eye-slash", "Hide")]

		this.setInputWidth($input);
		$input.after(this.renderButton(lbl[0], id, idShow));

		$("#" + id).on("click", function(e) {

			e.preventDefault();

			var $button = $(this);
			if($button.hasClass(idShow)) {
				$input[0].type = "text";
				$button.html(lbl[1]);
			} else {
				$input[0].type = "password";
				$button.html(lbl[0]);
			}

			$button.toggleClass(idShow + " " + id + "-hide");
		});
	}
};

$(document).ready(function() {
	InstagramBasicDisplayApiConfig.init();
});
