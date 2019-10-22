/**
 * Created by Administrator on 16-12-18.
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS之类的
        module.exports = factory();
    } else {
        // 浏览器全局变量(root 即 window)
        root.utils = factory();

        if (root.jQuery || root.Zepto) {
            var templates = {};
            $.convertTemplate = function (selector, data, fill) {
                var template = templates[selector];
                if (!template) {
                    template = $(selector).html();
                    templates[selector] = template;
                }
                return root.utils.convertTemplate(template, data, fill);
            }
        }
    }
}(this, function () {
    var resolve = function (key) {
        return '["' + key.replace(/\./g, '"]["') + '"]';
    };

    var getValue = function(flag, data) {
        var keys = flag.replace(/^\s+|\s+$/g, '').split(/\s*\|\s*/);
        var key = keys.shift();
        var val = '';

        eval('val=data' + resolve(key));

        for (var i=0;i<keys.length;i++) {
            var args = keys[i].split(/\s+/);
            var fn = data[args.shift()];

            if (fn && typeof fn === 'function') {
                for (var j=0;j<args.length;j++) {
                    var a = args[j];
                    if (/^this\..+/.test(a)) {
                        a = a.replace('this.', '');
                        a = 'data' + resolve(a);
                    }

                    eval('a=' + a);
                    args[j] = a;
                }

                args.unshift(val);

                val = fn.apply(null, args);
            }
        }

        return val;
    };

    return {
        convertTemplate: function (template, data, fill) {
            return template.replace(/\{#(.+?)#}/g, function ($0, $1) {
                var val = getValue($1, data);
                return typeof val !== 'undefined' ? val : (typeof fill === 'undefined' ? $0 : fill);
            })
        }
    };
}));
