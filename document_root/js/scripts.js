$(document).ready(function() {
    $('.flashMessage').delay(3000).fadeOut('fast');


    $('#frm-addRatingCategoriesForm table tr').hide();
    $('#frm-addRatingCategoriesForm table tr').eq(0).show();
    $('#frm-addRatingCategoriesForm table tr').eq(1).show();
    $('#frm-addRatingCategoriesForm table tr:last-child').show();
    $('#frm-addRatingCategoriesForm table tr:last-child').before('<tr><td></td><td><a href="#" id="showMoreRows">Přidat další kritérium</a></td></tr>');

    $('#showMoreRows').click(function() {
		$('#frm-addRatingCategoriesForm table tr').not(':visible').eq(0).show();
		$('#frm-addRatingCategoriesForm table tr').not(':visible').eq(0).show(); //intentionally showing two lines

		if ($('#frm-addRatingCategoriesForm table tr').not(':visible').length === 0) {
			$(this).hide();
		}
		return false;
    });

    $('h3.collapsibleForm').next('form').hide();
    $('h3.collapsibleForm a').click(function() {
		$(this).parent('h3').next('form').slideDown();
		return false;
	});
});
