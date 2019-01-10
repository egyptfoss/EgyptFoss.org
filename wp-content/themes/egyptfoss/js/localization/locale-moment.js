//! moment.js locale configuration
//! locale : Arabic (Saudi Arabia) [ar-sa]
//! author : Suhail Alkowaileet : https://github.com/xsoh

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment.js')) :
   typeof define === 'function' && define.amd ? define(['../moment.js'], factory) :
   factory(global.moment)
}(this, function (moment) { 'use strict';


    var symbolMap = {
        '1': '1',
        '2': '2',
        '3': '3',
        '4': '4',
        '5': '5',
        '6': '6',
        '7': '7',
        '8': '8',
        '9': '9',
        '0': '0'
    }, numberMap = {
        '1': '1',
        '2': '2',
        '3': '3',
        '4': '4',
        '5': '5',
        '6': '6',
        '7': '7',
        '8': '8',
        '9': '9',
        '0': '0'
    };

    var ar = moment.defineLocale('ar', {
        months : 'يناير_فبراير_مارس_أبريل_مايو_يونيو_يوليو_أغسطس_سبتمبر_أكتوبر_نوفمبر_ديسمبر'.split('_'),
        monthsShort : 'يناير_فبراير_مارس_أبريل_مايو_يونيو_يوليو_أغسطس_سبتمبر_أكتوبر_نوفمبر_ديسمبر'.split('_'),
        weekdays : 'الأحد_الإثنين_الثلاثاء_الأربعاء_الخميس_الجمعة_السبت'.split('_'),
        weekdaysShort : 'أحد_إثنين_ثلاثاء_أربعاء_خميس_جمعة_سبت'.split('_'),
        weekdaysMin : 'ح_ن_ث_ر_خ_ج_س'.split('_'),
        weekdaysParseExact : true,
        longDateFormat : {
            LT : 'HH:mm',
            LTS : 'HH:mm:ss',
            L : 'DD/MM/YYYY',
            LL : 'D MMMM YYYY',
            LLL : 'D MMMM YYYY HH:mm',
            LLLL : 'dddd D MMMM YYYY HH:mm'
        },
        meridiemParse: /ص|م/,
        isPM : function (input) {
            return 'م' === input;
        },
        meridiem : function (hour, minute, isLower) {
            if (hour < 12) {
                return 'ص';
            } else {
                return 'م';
            }
        },
        calendar : {
            sameDay: '[اليوم على الساعة] LT',
            nextDay: '[غدا على الساعة] LT',
            nextWeek: 'dddd [على الساعة] LT',
            lastDay: '[أمس على الساعة] LT',
            lastWeek: 'dddd [على الساعة] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : 'في %s',
            past : 'منذ %s',
            s : 'ثوان',
            m : 'دقيقة',
            mm : '%d دقائق',
            h : 'ساعة',
            hh : '%d ساعات',
            d : 'يوم',
            dd : '%d أيام',
            M : 'شهر',
            MM : '%d أشهر',
            y : 'سنة',
            yy : '%d سنوات'
        },
        preparse: function (string) {
            return string.replace(/[1234567890]/g, function (match) {
                return numberMap[match];
            }).replace(/،/g, ',');
        },
        postformat: function (string) {
            return string.replace(/\d/g, function (match) {
                return symbolMap[match];
            }).replace(/,/g, '،');
        },
        week : {
            dow : 6, // Saturday is the first day of the week.
            doy : 12  // The week that contains Jan 1st is the first week of the year.
        }
    });

    return ar;

}));