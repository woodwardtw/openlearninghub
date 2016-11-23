(function () {
    var a = tinymce.DOM;
    tinymce.create("tinymce.plugins.WooThemesColumnGenerator", {
        mceTout: 0,
        init: function (c, d) { // c = editor instance, d = URL to current directory
        
        	c.addButton('WooThemesColumnGenerator', {
                title: "Add Column Break", // WooThemesColumnGenerator.woo_column_desc
                image: d + "/img/icon.png",
                cmd: "Woo_Column"
            });
        
            var e = this,
                h = c.getParam("wordpress_adv_toolbar", "toolbar2"),
                g = 0,
                f, b;
                // c.getLang("WooThemesColumnGenerator.woo_column_alt")
            f = '<img src="' + d + '/img/trans.gif" class="mceWooColumn mceItemNoResize" title="' + 'Column' + '" />';
            c.onPostRender.add(function () {
                var i = c.controlManager.get(h);
                if (c.getParam("wordpress_adv_hidden", 1) && i) {
                    a.hide(i.id);
                    e._resizeIframe(c, h, 28)
                }
            });
            
            c.addCommand("Woo_Column", function () {
                c.execCommand("mceInsertContent", 0, f)
            });
            
            c.onBeforeExecCommand.add(function (p, m, s, l, j) {
                var v = tinymce.DOM,
                    k, i, r, u, t, q;
            });
            c.onInit.add(function (i) {
                i.onBeforeSetContent.add(function (j, k) {
                    if (k.content) {
                        k.content = k.content.replace(/<p>\s*<(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)( [^>]*)?>/gi, "<$1$2>");
                        k.content = k.content.replace(/<\/(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre|address)>\s*<\/p>/gi, "</$1>")
                    }
                })
            });
            if ("undefined" != typeof wpWordCount) {
                c.onKeyUp.add(function (i, j) {
                    if (j.keyCode == g) {
                        return
                    }
                    if (13 == j.keyCode || 8 == g || 46 == g) {
                        wpWordCount.wc(i.getContent({
                            format: "raw"
                        }))
                    }
                    g = j.keyCode
                })
            }
            c.onSaveContent.add(function (i, j) {
                if (typeof (switchEditors) == "object") {
                    if (i.isHidden()) {
                        j.content = j.element.value
                    } else {
                        j.content = switchEditors.pre_wpautop(j.content)
                    }
                }
            });
            e._handleColumnBreak(c, d);
            // c.addShortcut("alt+shift+t", c.getLang("woo_column_desc"), "Woo_Column");
            c.onInit.add(function (i) {
                tinymce.dom.Event.add(i.getWin(), "scroll", function (j) {
                    i.plugins.WooThemesColumnGenerator._hideButtons()
                });
                tinymce.dom.Event.add(i.getBody(), "dragstart", function (j) {
                    i.plugins.WooThemesColumnGenerator._hideButtons()
                })
            });
            c.onBeforeExecCommand.add(function (i, k, j, l) {
                i.plugins.WooThemesColumnGenerator._hideButtons()
            });
            c.onSaveContent.add(function (i, j) {
                i.plugins.WooThemesColumnGenerator._hideButtons()
            });
            c.onMouseDown.add(function (i, j) {
                if (j.target.nodeName != "IMG") {
                    i.plugins.WooThemesColumnGenerator._hideButtons()
                }
            })
        },
        
        createControl : function(n, cm) {
            return null;
        },
        
        getInfo: function () {
            return {
                longname: "WooThemes Column Generator",
                author: "WooThemes",
                authorurl: "http://woothemes.com/",
                infourl: "http://woothemes.com/",
                version: "1.0.0"
            }
        },
        _showButtons: function (f, d) {
            var g = tinyMCE.activeEditor,
                i, h, b, j = tinymce.DOM,
                e, c;
            b = g.dom.getViewPort(g.getWin());
            i = j.getPos(g.getContentAreaContainer());
            h = g.dom.getPos(f);
            e = Math.max(h.x - b.x, 0) + i.x;
            c = Math.max(h.y - b.y, 0) + i.y;
            j.setStyles(d, {
                top: c + 5 + "px",
                left: e + 5 + "px",
                display: "block"
            });
            if (this.mceTout) {
                clearTimeout(this.mceTout)
            }
            this.mceTout = setTimeout(function () {
                g.plugins.WooThemesColumnGenerator._hideButtons()
            }, 5000)
        },
        _hideButtons: function () {
            if (!this.mceTout) {
                return
            }
            clearTimeout(this.mceTout);
            this.mceTout = 0
        },
        _resizeIframe: function (c, e, b) {
            var d = c.getContentAreaContainer().firstChild;
            a.setStyle(d, "height", d.clientHeight + b);
            c.theme.deltaHeight += b
        },
        _handleColumnBreak: function (c, d) {
            var e, b;
            e = '<img src="' + d + '/img/trans.gif" alt="$1" class="mceWooColumn mceItemNoResize" title="' + 'Column' + '" />';
            // c.getLang("WooThemesColumnGenerator.woo_column_alt")
            c.onInit.add(function () {
                c.dom.loadCSS(d + "/css/content.css")
            });
            c.onPostRender.add(function () {
                if (c.theme.onResolveName) {
                    c.theme.onResolveName.add(function (f, g) {
                        if (g.node.nodeName == "IMG") {
                            if (c.dom.hasClass(g.node, "mceWooColumn")) {
                                g.name = "woocolumn"
                            }
                        }
                    })
                }
            });
            c.onBeforeSetContent.add(function (f, g) {
                if (g.content) {
                    g.content = g.content.replace(/<!--column(.*?)-->/g, e);
                }
            });
            c.onPostProcess.add(function (f, g) {
                if (g.get) {
                    g.content = g.content.replace(/<img[^>]+>/g, function (i) {
                        if (i.indexOf('class="mceWooColumn') !== -1) {
                            var h, j = (h = i.match(/alt="(.*?)"/)) ? h[1] : "";
                            i = "<!--column" + j + "-->"
                        }
                        return i
                    })
                }
            });
            c.onNodeChange.add(function (g, f, h) {
                f.setActive("Woo_Column", h.nodeName === "IMG" && g.dom.hasClass(h, "mceWooColumn"))
            })
        }
    });
    tinymce.PluginManager.add("WooThemesColumnGenerator", tinymce.plugins.WooThemesColumnGenerator)
})();