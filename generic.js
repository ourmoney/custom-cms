var sliders = [];

function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,})+$/;
    return regex.test(email);
}

function bindMeetTheTeamOverlays() {
    $(".meet-the-team .meet-the-team-box").each(function() {
        var person = $(this);
        var overlay = $(this).find('.overlay').first();

        person.unbind('mouseenter');
        person.unbind('mouseleave');
        person.hover(function() {
            overlay.clearQueue();
            overlay.stop();
            overlay.animate({
                height: '100%'
            }, 500);
        }, function() {
            overlay.clearQueue();
            overlay.stop();
            overlay.animate({
                height: 65
            }, 500);
        });
    });
}

function resizeCaseStudy() {
    if ($('#homepage').size() > 0) {
        if ($(window).width() > 960) {
            var h = $('.primary .service').outerHeight();
            var w = $('.primary .case-study').outerWidth();
            $('.primary .case-study').css({
                'height': h + 'px'
            })
            $('.primary .case-study .bxslider').css({
                'height': h + 'px'
            })
            $('.primary .case-study .bxslider .slide .image').css({
                'height': h + 'px'
            });
            $('.primary .case-study .bxslider .slide .image img').css({
                'height': 'auto',
                'width': w + 'px',
                'margin': 'auto 0'
            });
        } else {
            $('.primary .case-study').css({
                'height': 'auto'
            })
            $('.primary .case-study .bxslider').css({
                'height': 'auto'
            })
        }
        sliders[1].reloadSlider()
    }


    if ($('.double-column .case-study').size()) {
        var h = 400;
        var w = $('.double-column .case-study').outerWidth();
        $('.double-column .case-study').css({
            'height': h + 'px'
        })
        $('.double-column .case-study .bxslider').css({
            'height': h + 'px'
        })
        $('.double-column .case-study .bxslider .slide .image').css({
            'height': h + 'px',
            'width': w + 'px'
        });
        $('.double-column .case-study .bxslider .slide .image img').css({
            'height': h + 'px',
            'width': w + 'px',
            'margin': 'auto 0',
            
        });

        sliders[0].reloadSlider()
    }

}

function resizeSeriveSectors() {
    resizeCaseStudy();
    if ($('#homepage').size() > 0) {
        $('.homepagecarousel .videoslide').each(function() {
            if ($(window).width() < 1920) {
                var p = Math.round(1920 - $(window).width()) / 2;
                if ($(this).find('video').length > 0) {
                    $(this).find('video').css({
                        'left': '-' + p + 'px',
                        'width': ($(window).width() + (p * 2)) + 'px',
                        'object-fit': 'full'
                    })
                }

            } else {
                if ($(this).find('video').length > 0) {
                    $(this).find('video').css({
                        'left': '0',
                        'width': '100%'
                    })
                }
            }
        })

        if ($(window).width() > 960) {
            var h = [];
            $('.service-sector aside').each(function() {
                h.push($(this).outerHeight())
            })
            h.sort();
            var th = h[h.length - 1];

            $('.service-sector aside').each(function() {
                $(this).find('> div').css({
                    'height': th + 'px'
                })
            })

        } else {
            $('.service-sector aside').each(function() {
                $(this).find('> div').css({
                    'height': 'auto'
                });
            })
        }
    }
}

$(document).ready(function() {
    $('body').on('click', 'a.new-window', function(event) {
        event.preventDefault();
        window.open($(this).attr('href'), 'NewWindow');
    });

    /***** Bootstrap validation ************/

    $('#contactForm').validator({
        framework: 'bootstrap',
        err: {
            container: '#messages'
        },
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            firstname: {
                validators: {
                    notEmpty: {
                        message: 'The full name is required and cannot be empty'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email address is required and cannot be empty'
                    },
                    emailAddress: {
                        message: 'The email address is not valid'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: 'The title is required and cannot be empty'
                    },
                    stringLength: {
                        max: 16,
                        min: 11,
                        message: 'The Phone number must between 11-16 character long',
                        checkPhone: true
                    }
                }
            },
            enquiry: {
                validators: {
                    notEmpty: {
                        message: 'The enquiry is required and cannot be empty'
                    },
                    stringLength: {
                        max: 500,
                        message: 'The enquiry must be less than 500 characters long'
                    }
                }
            }
        }
    });

    $.validator.addMethod('checkPhone', function(value, element) {
        var count=value.match(/\d/g).length;
        if(count >= 9)
            return true;
        else
            return false;       
    }, "Telephone number should contain at least 9 digits");

    $('#contact-form').validate({
        rules: {
            fullname: {
                required: true
            },
            telephone: {
                required: true,
                minlength: 9,
                maxlength: 20,
                checkPhone: true
            },

            email: {
                required: true,
                email: true
            },

        },
        highlight: function(element) {
            $(element).closest('.control-group').removeClass('success').addClass('error');
        },
        success: function(element) {
            element
                .text('OK!').addClass('valid')
                .closest('.control-group').removeClass('error').addClass('success');
        }
    });

    $('.overlay-full').hover(function() {
        //$('.overlay-full').animate({addClass:'overlay-hover'},500);
        $(this).addClass('overlay-hover');
    }, function() {
        $(this).removeClass('overlay-hover');

    });

    $(".patent-sector .overlay").each(function() {
        var id = "#" + this.id;

        $(id).hover(function() {
            var $overlay = $(id);
            $overlay.animate({
                height: '75%'
            }, 500);
        }, function() {
            var $overlay = $(id);
            $overlay.animate({
                height: 100
            }, 500);
        })

        $(id).click(function() {
            location.href = $(id).data('url');
        })
    });

    $('.btn-show-search').click(function() {
        //$('.contact-information-box').slideToggle();
        /*  $("#top-search").animate({
                width: "toggle",
                direction: "left"
            });*/

        // Set the effect type
        var effect = 'slide';

        // Set the options for the effect type chosen
        var options = {
            direction: 'right'
        };

        // Set the duration (default: 400 milliseconds)
        var duration = 100;

        $('#top-search').toggle(effect, options, duration);

    });

    $('#icon-contact').click(function(e) {
        e.preventDefault('');
        if ($(this).hasClass('sel')) {
            $(this).removeClass('sel');
            $('.contact-information-box').slideUp('fast');
        } else {
            $(this).addClass('sel');
            $('.contact-information-box').slideDown('fast');
            $('#header-search').hide();
            $('#icon-search').removeClass('sel');
            $('#topnav').hide();
            $('#icon-menu').removeClass('sel');
        }
    })

    $('#icon-search').click(function(e) {
        e.preventDefault('');

        if ($(this).hasClass('sel')) {
            $(this).removeClass('sel');
            if ($('#icon-menu:visible').length > 0) {
                $('#header-search').slideUp('fast');
            } else {
                $('#header-search').submit();
            }
        } else {
            $(this).addClass('sel');
            if ($('#icon-menu:visible').length > 0) {
                $('#header-search').slideDown('fast');
            } else {
                $('#header-search').submit();
            }

            $('.contact-information-box').hide();
            $('#icon-contact').removeClass('sel');
            $('#topnav').hide();
            $('#icon-menu').removeClass('sel');
        }
    })

    $('#icon-menu').click(function(e) {
        e.preventDefault('');
        if ($(this).hasClass('sel')) {
            $(this).removeClass('sel');
            $('#topnav').slideUp('fast');
        } else {
            $(this).addClass('sel');
            $('#topnav').slideDown('fast');
            $('#header-search').hide();
            $('#icon-search').removeClass('sel');
            $('.contact-information-box').hide();
            $('#icon-contact').removeClass('sel');
        }
    })


    if ($('#icon-menu:visible').length > 0) {

        $('#topnav .dropdown a').click(function(e) {

            if ($(this).parent('.dropdown').length > 0) {
                e.preventDefault('');
                $('.all a').removeClass();
            }

            //$(this).addClass('sel');
            $(this).next().removeClass();
        })
    }

    bindMeetTheTeamOverlays();

    $('#people-serch-keywords').click(function(e) {

        e.preventDefault('');

        $(".dropdownMenuServices").attr('id')

        var sector_term = $(".dropdownMenuSector").attr('id');
        var services_term = $(".dropdownMenuServices").attr('id');
        var keywords_term = this.value;

        $.ajax({
            method: "POST",
            url: "/process/get-people.php",
            data: {
                'keywords_term': keywords_term,
                'sector_term': sector_term,
                'services_term': services_term
            },
            success: function(msg) {
                $("#team").html(msg);
                bindMeetTheTeamOverlays();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            }
        });
    })

    $('.sector li').click(function(e) {
        e.preventDefault();
        var sector_term = $(this).data('value');
        var services_term = $("#services-dropdown").data('value');
        var keywords_term = $("#people-serch-keywords").val();


        lbl = $(this).text();
        $('#sectors-dropdown').text(lbl);
        $('#sectors-dropdown').data('value', sector_term);

        $.ajax({
            method: "POST",
            url: "/process/get-people.php",
            data: {
                'keywords_term': keywords_term,
                'sector_term': sector_term,
                'services_term': services_term
            },
            success: function(msg) {
                $("#team").html(msg);
                bindMeetTheTeamOverlays();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            }
        });
    })

    $('.services li').click(function(e) {
        e.preventDefault('');
        var sector_term = $("#sectors-dropdown").data('value');
        var services_term = $(this).data('value');
        var keywords_term = $("#people-serch-keywords").val();

        lbl = $(this).text();
        $("#services-dropdown").text(lbl);
        $("#services-dropdown").data(services_term);

        $.ajax({
            method: "POST",
            url: '/process/get-people.php',
            data: {
                'keywords_term': keywords_term,
                'sector_term': sector_term,
                'services_term': services_term
            },
            success: function(msg) {
                $("#team").html(msg);
                bindMeetTheTeamOverlays();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            }
        });
    })

    if ($('.btn-get-articles').length > 0) {
        $('.btn-get-articles').click(function() {
            var btn = $(this);

            $.ajax({
                method: "POST",
                url: '/process/get-articles.php',
                data: {
                    'section': btn.data('section'),
                    'limit': btn.data('limit'),
                    'offset': btn.data('offset')
                },
                success: function(data) {
                    $('.articles-list').append(data);

                    btn.data('offset', $('.articles-list .article').length);

                    if (btn.data('offset') >= btn.data('end')) {
                        btn.remove();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //
                }
            });
        });
    }

    if ($('#job-application-form').length > 0) {
        $('#job-application-form').validate({
            rules: {
                firstname: {
                    required: true
                },
                lastname: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                telephone: {
                    required: true,
                    minlength: 9,
                    maxlength: 20,
                    checkPhone: true
                },
                coveringletter: {
                    required: true,
                },
                cv: {
                    required: true,
                },
            },
            highlight: function(element) {
                $(element).closest('.control-group').removeClass('success').addClass('error');
            },
            success: function(element) {
                element.text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
            }
        });

        $('#job-application-form').ajaxForm({
            success: function(data) {
                window.location.href = data;
            },
            error: function(jqXHR,textStatus,errorThrown) {
                $('.error').text(errorThrown).show();
            }
        });
    }
});

$(document).ajaxStart(function() {
    $('html,body').addClass("wait");
});

$(document).ajaxStop(function() {
    $('html,body').removeClass("wait");
});

$(window).resize(function() {
    resizeSeriveSectors();
})

$(window).load(function() {
        if ($('.page-body p').size() > 1) {
            $('.page-body p').eq(0).addClass('lede');
        }
        if ($('.bxslider').size() > 0) {
            $('.bxslider').each(function() {
                var bx = $(this);
                if (bx.hasClass('homepagecarousel')) {
                    sliders[sliders.length] = bx.bxSlider({
                        auto: true,
                        pause: 10000,
                        controls: false,
                        infiniteLoop: true,
                        hideControlOnEnd: true,
                        onSliderLoad: function() {
                            var f = bx.find('.slide').eq(1);
                            if (f.find('video').length > 0) {
                                f.find('video').get(0).play();
                            }
                        },
                        onSlideAfter: function($slideElement) {
                            if ($slideElement.find('video').length > 0) {
                                $slideElement.find('video').get(0).play();
                            }
                        },
                        onSlideBefore: function($slideElement) {
                            if ($slideElement.find('video').length > 0) {
                                $slideElement.find('video').get(0).pause();
                            }
                        }
                    });
                } else {                    
                    if (bx.hasClass('noauto')) {
                        sliders[sliders.length] = bx.bxSlider({                            
                            auto: false,
                            //pager:false,
                            pager: ($(".bxslider > .news").length > 1) ? true : false,
                            controls: false,
                            infiniteLoop: true,
                            hideControlOnEnd: true
                        });
                    } else {
                        sliders[sliders.length] = bx.bxSlider({
                            auto: ($(".bxslider > .slide").length > 1) ? true : false,
                            pager: ($(".bxslider > .slide").length > 1) ? true : false,
                            controls: false,
                            infiniteLoop: true,
                            hideControlOnEnd: true,
                            pause: 6000
                        });
                    }
                }
            })
        }

        maxheight = $(".patent-sector .overlay .heading-text").maxHeights();
        minheight = $(".patent-sector .overlay .heading-text").minHeights();

        $(".patent-sector .overlay .heading-text").each(function() {
            h = $(this).height();

            if (h == maxheight)
                topmargin = (maxheight - minheight) / 2;
            else
                topmargin = maxheight - h;

            $(this).css({
                top: topmargin + "%"
            });
        });

        resizeSeriveSectors();
    })
    /*
    $.fn.equalizeHeights = function() {
      var maxHeight = this.map(function(i,e) {
        return $( e ).height();
      }).get();
      return Math.min.apply( this, maxHeight );
    };*/
$.fn.minHeights = function() {
    var maxHeight = this.map(function(i, e) {
        return $(e).height();
    }).get();
    return Math.min.apply(this, maxHeight);
};
$.fn.maxHeights = function() {
    var maxHeight = this.map(function(i, e) {
        return $(e).height();
    }).get();
    return Math.max.apply(this, maxHeight);
};