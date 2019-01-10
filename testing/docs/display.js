
$(document).ready(function () {
    $('.close').hide();
    $('#sidebar1').css({left: '0%', width: '100%'});
    $('#sidebar2').css({left: '100%', width: '0%', });
    $('#sidebar3').css({left: '100%', width: '0%', });

    $('[data-toggle="collapse"]').click(function () {
        jQuery(this).toggleClass('active');
    });

    $('.close').click(function () {
        $('.close').hide();
        $('#sidebar1').css({left: '0%', width: '100%'});
        $('#sidebar2').css({left: '100%', width: '0%', });
        $('#sidebar3').css({left: '100%', width: '0%', });
    });
});

function initDisplay() {
    $('.feature tr').click(function () {
        $('#sidebar1').css({width: '50%'});
        $('#sidebar2').css({left: '50%', width: '50%', });
        $('#sidebar3').css({left: '100%', width: '0%', });
        $('.feature_title').text(jQuery(this).text());

        $('.close').show();
        $suiteId = jQuery(this).attr("suite");

        $('.suite_title').text(jQuery(this).attr("suitename"));
        $featureId = jQuery(this).attr("feature");

        $scenariono = $("[suite][feature][scenario]");
        $scenariono.addClass('hidden');

        features = $(".scenario[suite='" + $suiteId + "'][feature='" + $featureId + "'][scenario]");
        features.removeClass('hidden');

        activeFeatures = $('.feature.active').removeClass('active');
        jQuery(this).toggleClass('active');

    });
    $('.scenario').click(function () {
        $('#sidebar1').css({width: '30%'});
        $('#sidebar2').css({left: '10%', width: '40%', });
        $('#sidebar3').css({left: '50%', width: '50%', });

        $('.scenario_title').text(jQuery(this).text());

        $('.close').show();
        $suiteId = jQuery(this).attr("suite");
        $featureId = jQuery(this).attr("feature");
        $scenarioId = jQuery(this).attr("scenario");

        $scenariono = $("[suite][feature][scenario][step]");
        $scenariono.addClass('hidden');

        $backno = $("[suite][feature][background]");
        $backno.addClass('hidden');

        $featureback = $("[suite='" + $suiteId + "'][feature='" + $featureId + "'][background]");
        $featureback.removeClass('hidden');

        $example = $("[suite][feature][scenario][example]");
        $example.addClass('hidden');
        $featureexample = $("[suite='" + $suiteId + "'][feature='" + $featureId + "'][scenario='" + $scenarioId + "'][example]");
        $featureexample.removeClass('hidden');

        $scenarios = $(".step[suite='" + $suiteId + "'][feature='" + $featureId + "'][scenario='" + $scenarioId + "'][step]");
        $scenarios.removeClass('hidden');

        activeFeatures = $('.scenario.active').removeClass('active');
        jQuery(this).toggleClass('active');
    });
}