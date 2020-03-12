/**
 * Instagram Basic Display API Config JS
 *
 * @copyright 2020 NB Communication Ltd
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 *
 */

var InstagramBasicDisplayApiConfig = {

	init: function() {
		this.iconFix();
		this.trash();
	},

	iconFix: function() { // For PW 2.7.2

		var $spans = $("input.remove-account + span");
		if(!$spans.length) return;

		$spans.each(function() {
			var $span = $(this);
			if(!$span.children("i").length) {
				$span.html("<i class='fa fa-trash'></i>");
			}
		});
	},

	trash: function() {

		var $inputs = $("input.remove-account");
		if(!$inputs.length) return;

		$inputs.on("click", function() {
			var $input = $(this);
			var $row = $input.closest("tr");
			if($input.is(":checked")) {
				if(confirm("Are you sure you want to remove " + $input.val() + "?\nThe account will be removed once you click Submit.")) {
					$row.addClass("removing-account");
				} else {
					$input.attr("checked", false);
				}
			} else {
				$row.removeClass("removing-account");
			}
		});
	}
};

$(document).ready(function() {
	InstagramBasicDisplayApiConfig.init();
});
