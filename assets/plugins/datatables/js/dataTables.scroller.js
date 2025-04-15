/*! Scroller 2.4.0
 * © SpryMedia Ltd - datatables.net/license
 */
!(function (o) {
  var e, i;
  "function" == typeof define && define.amd
    ? define(["jquery", "datatables.net"], function (t) {
        return o(t, window, document);
      })
    : "object" == typeof exports
    ? ((e = require("jquery")),
      (i = function (t, s) {
        s.fn.dataTable || require("datatables.net")(t, s);
      }),
      "undefined" == typeof window
        ? (module.exports = function (t, s) {
            return (
              (t = t || window), (s = s || e(t)), i(t, s), o(s, t, t.document)
            );
          })
        : (i(window, e), (module.exports = o(e, window, window.document))))
    : o(jQuery, window, document);
})(function (d, l, o) {
  "use strict";
  function i(t, s) {
    this instanceof i
      ? (void 0 === s && (s = {}),
        (t = d.fn.dataTable.Api(t)),
        (this.s = {
          dt: t.settings()[0],
          dtApi: t,
          tableTop: 0,
          tableBottom: 0,
          redrawTop: 0,
          redrawBottom: 0,
          autoHeight: !0,
          viewportRows: 0,
          stateTO: null,
          stateSaveThrottle: function () {},
          drawTO: null,
          heights: {
            jump: null,
            page: null,
            virtual: null,
            scroll: null,
            row: null,
            viewport: null,
            labelHeight: 0,
            xbar: 0,
          },
          topRowFloat: 0,
          scrollDrawDiff: null,
          loaderVisible: !1,
          forceReposition: !1,
          baseRowTop: 0,
          baseScrollTop: 0,
          mousedown: !1,
          lastScrollTop: 0,
        }),
        (this.s = d.extend(this.s, i.oDefaults, s)),
        (this.s.heights.row = this.s.rowHeight),
        (this.dom = {
          force: o.createElement("div"),
          label: d('<div class="dts_label">0</div>'),
          scroller: null,
          table: null,
          loader: null,
        }),
        this.s.dt.oScroller || (this.s.dt.oScroller = this).construct())
      : alert(
          "Scroller warning: Scroller must be initialised with the 'new' keyword."
        );
  }
  var a = d.fn.dataTable,
    t =
      (d.extend(i.prototype, {
        measure: function (t) {
          this.s.autoHeight && this._calcRowHeight();
          var s = this.s.heights,
            o =
              (s.row &&
                ((s.viewport = this._parseHeight(
                  d(this.dom.scroller).css("max-height")
                )),
                (this.s.viewportRows = parseInt(s.viewport / s.row, 10) + 1),
                (this.s.dt._iDisplayLength =
                  this.s.viewportRows * this.s.displayBuffer)),
              this.dom.label.outerHeight());
          (s.xbar =
            this.dom.scroller.offsetHeight - this.dom.scroller.clientHeight),
            (s.labelHeight = o),
            (void 0 !== t && !t) || this.s.dt.oInstance.fnDraw(!1);
        },
        pageInfo: function () {
          var t = this.s.dt,
            s = this.dom.scroller.scrollTop,
            t = t.fnRecordsDisplay(),
            o = Math.ceil(
              this.pixelsToRow(s + this.s.heights.viewport, !1, this.s.ani)
            );
          return {
            start: Math.floor(this.pixelsToRow(s, !1, this.s.ani)),
            end: t < o ? t - 1 : o - 1,
          };
        },
        pixelsToRow: function (t, s, o) {
          (t -= this.s.baseScrollTop),
            (o = o
              ? (this._domain("physicalToVirtual", this.s.baseScrollTop) + t) /
                this.s.heights.row
              : t / this.s.heights.row + this.s.baseRowTop);
          return s || void 0 === s ? parseInt(o, 10) : o;
        },
        rowToPixels: function (t, s, o) {
          (t -= this.s.baseRowTop),
            (o = o
              ? this._domain("virtualToPhysical", this.s.baseScrollTop)
              : this.s.baseScrollTop);
          return (
            (o += t * this.s.heights.row),
            s || void 0 === s ? parseInt(o, 10) : o
          );
        },
        scrollToRow: function (t, s) {
          var o = this,
            e = !1,
            i = this.rowToPixels(t),
            r = t - ((this.s.displayBuffer - 1) / 2) * this.s.viewportRows;
          r < 0 && (r = 0),
            void 0 ===
              (s =
                (i > this.s.redrawBottom || i < this.s.redrawTop) &&
                this.s.dt._iDisplayStart !== r &&
                ((e = !0),
                (i = this._domain("virtualToPhysical", t * this.s.heights.row)),
                this.s.redrawTop < i) &&
                i < this.s.redrawBottom
                  ? !(this.s.forceReposition = !0)
                  : s) || s
              ? ((this.s.ani = e),
                d(this.dom.scroller).animate({ scrollTop: i }, function () {
                  setTimeout(function () {
                    o.s.ani = !1;
                  }, 250);
                }))
              : d(this.dom.scroller).scrollTop(i);
        },
        construct: function () {
          var e = this,
            t = this.s.dtApi;
          if (!this.s.dt.oFeatures.bPaginate)
            throw new Error(
              "Pagination must be enabled for Scroller to operate"
            );
          (this.dom.force.style.position = "relative"),
            (this.dom.force.style.top = "0px"),
            (this.dom.force.style.left = "0px"),
            (this.dom.force.style.width = "1px"),
            (this.dom.scroller = t.table().node().parentNode),
            this.dom.scroller.appendChild(this.dom.force),
            (this.dom.scroller.style.position = "relative"),
            (this.dom.table = d(">table", this.dom.scroller)[0]),
            (this.dom.table.style.position = "absolute"),
            (this.dom.table.style.top = "0px"),
            (this.dom.table.style.left = "0px"),
            d(t.table().container()).addClass("dts DTS"),
            this.dom.label.appendTo(this.dom.scroller),
            this.s.heights.row &&
              "auto" != this.s.heights.row &&
              (this.s.autoHeight = !1),
            (this.s.ingnoreScroll = !0),
            d(this.dom.scroller).on("scroll.dt-scroller", function (t) {
              e._scroll.call(e);
            }),
            d(this.dom.scroller).on("touchstart.dt-scroller", function () {
              e._scroll.call(e);
            }),
            d(this.dom.scroller)
              .on("mousedown.dt-scroller", function () {
                e.s.mousedown = !0;
              })
              .on("mouseup.dt-scroller", function () {
                (e.s.labelVisible = !1),
                  (e.s.mousedown = !1),
                  e.dom.label.css("display", "none");
              }),
            d(l).on("resize.dt-scroller", function () {
              e.measure(!1), e._info();
            });
          var i = !0,
            r = t.state.loaded();
          t.on("stateSaveParams.scroller", function (t, s, o) {
            i && r
              ? ((o.scroller = r.scroller),
                (i = !1),
                o.scroller && (e.s.lastScrollTop = o.scroller.scrollTop))
              : (o.scroller = {
                  topRow: e.s.topRowFloat,
                  baseRowTop: e.s.baseRowTop,
                });
          }),
            t.on("stateLoadParams.scroller", function (t, s, o) {
              void 0 !== o.scroller && e.scrollToRow(o.scroller.topRow);
            }),
            this.measure(!1),
            r &&
              r.scroller &&
              ((this.s.topRowFloat = r.scroller.topRow),
              (this.s.baseRowTop = r.scroller.baseRowTop),
              (this.s.baseScrollTop = this.s.baseRowTop * this.s.heights.row),
              (r.scroller.scrollTop = this._domain(
                "physicalToVirtual",
                this.s.topRowFloat * this.s.heights.row
              ))),
            (e.s.stateSaveThrottle = a.util.throttle(function () {
              e.s.dtApi.state.save();
            }, 500)),
            t.on("init.scroller", function () {
              e.measure(!1),
                (e.s.scrollType = "jump"),
                e._draw(),
                t.on("draw.scroller", function () {
                  e._draw();
                });
            }),
            t.on("preDraw.dt.scroller", function () {
              e._scrollForce();
            }),
            t.on("destroy.scroller", function () {
              d(l).off("resize.dt-scroller"),
                d(e.dom.scroller).off(".dt-scroller"),
                d(e.s.dt.nTable).off(".scroller"),
                d(e.s.dt.nTableWrapper).removeClass("DTS"),
                d("div.DTS_Loading", e.dom.scroller.parentNode).remove(),
                (e.dom.table.style.position = ""),
                (e.dom.table.style.top = ""),
                (e.dom.table.style.left = "");
            });
        },
        _calcRowHeight: function () {
          var t = this.s.dt,
            s = t.nTable,
            o = s.cloneNode(!1),
            e = d("<tbody/>").appendTo(o),
            t = t.oClasses,
            t = a.versionCheck("2")
              ? {
                  container: t.container,
                  scroller: t.scrolling.container,
                  body: t.scrolling.body,
                }
              : {
                  container: t.sWrapper,
                  scroller: t.sScrollWrapper,
                  body: t.sScrollBody,
                },
            i = d(
              '<div class="' +
                t.container +
                ' DTS"><div class="' +
                t.scroller +
                '"><div class="' +
                t.body +
                '"></div></div></div>'
            ),
            r = (d("tbody tr:lt(4)", s).clone().appendTo(e), d("tr", e).length);
          if (1 === r)
            e.prepend("<tr><td>&#160;</td></tr>"),
              e.append("<tr><td>&#160;</td></tr>");
          else for (; r < 3; r++) e.append("<tr><td>&#160;</td></tr>");
          d("div." + t.body, i).append(o);
          t = this.s.dt.nHolding || s.parentNode;
          d(t).is(":visible") || (t = "body"),
            i.find("input").removeAttr("name"),
            i.appendTo(t),
            (this.s.heights.row = d("tr", e).eq(1).outerHeight()),
            i.remove();
        },
        _draw: function () {
          var t = this,
            s = this.s.heights,
            o = this.dom.scroller.scrollTop,
            e = d(this.s.dt.nTable).height(),
            i = this.s.dt._iDisplayStart,
            r = this.s.dt._iDisplayLength,
            l = this.s.dt.fnRecordsDisplay(),
            a = o + s.viewport,
            n =
              ((this.s.skip = !0),
              (!this.s.dt.bSorted && !this.s.dt.bFiltered) ||
                0 !== i ||
                this.s.dt._drawHold ||
                (this.s.topRowFloat = 0),
              (o =
                "jump" === this.s.scrollType
                  ? this._domain(
                      "virtualToPhysical",
                      this.s.topRowFloat * s.row
                    )
                  : o),
              (this.s.baseScrollTop = o),
              (this.s.baseRowTop = this.s.topRowFloat),
              o - (this.s.topRowFloat - i) * s.row),
            i =
              (0 === i
                ? (n = 0)
                : l <= i + r
                ? (n = s.scroll - e)
                : n + e < a &&
                  ((this.s.baseScrollTop += 1 + ((l = a - e) - n)), (n = l)),
              (this.dom.table.style.top = n + "px"),
              (this.s.tableTop = n),
              (this.s.tableBottom = e + this.s.tableTop),
              (o - this.s.tableTop) * this.s.boundaryScale);
          (this.s.redrawTop = o - i),
            (this.s.redrawBottom =
              o + i > s.scroll - s.viewport - s.row
                ? s.scroll - s.viewport - s.row
                : o + i),
            (this.s.skip = !1),
            t.s.ingnoreScroll &&
              (this.s.dt.oFeatures.bStateSave &&
              null !== this.s.dt.oLoadedState &&
              void 0 !== this.s.dt.oLoadedState.scroller
                ? (((r = !(
                    (!this.s.dt.sAjaxSource && !t.s.dt.ajax) ||
                    this.s.dt.oFeatures.bServerSide
                  )) &&
                    2 <= this.s.dt.iDraw) ||
                    (!r && 1 <= this.s.dt.iDraw)) &&
                  setTimeout(function () {
                    d(t.dom.scroller).scrollTop(
                      t.s.dt.oLoadedState.scroller.scrollTop
                    ),
                      setTimeout(function () {
                        t.s.ingnoreScroll = !1;
                      }, 0);
                  }, 0)
                : (t.s.ingnoreScroll = !1)),
            this.s.dt.oFeatures.bInfo &&
              setTimeout(function () {
                t._info.call(t);
              }, 0),
            d(this.s.dt.nTable).triggerHandler("position.dts.dt", n);
        },
        _domain: function (t, s) {
          var o,
            e = this.s.heights,
            i = 1e4;
          return e.virtual === e.scroll || s < i
            ? s
            : "virtualToPhysical" === t && s >= e.virtual - i
            ? ((o = e.virtual - s), e.scroll - o)
            : "physicalToVirtual" === t && s >= e.scroll - i
            ? ((o = e.scroll - s), e.virtual - o)
            : ((e = i - (o = (e.virtual - i - i) / (e.scroll - i - i)) * i),
              "virtualToPhysical" === t ? (s - e) / o : o * s + e);
        },
        _info: function () {
          if (this.s.dt.oFeatures.bInfo) {
            var t = this.s.dt,
              s = this.s.dtApi,
              o = t.oLanguage,
              e = s.page.info(),
              i = e.recordsDisplay,
              e = e.recordsTotal,
              r =
                (this.s.lastScrollTop - this.s.baseScrollTop) /
                this.s.heights.row,
              r = Math.floor(this.s.baseRowTop + r),
              l =
                (r =
                  "jump" === this.s.scrollType
                    ? Math.floor(this.s.topRowFloat) + 1
                    : r) +
                Math.floor(this.s.heights.viewport / this.s.heights.row),
              l = i < l ? i : l,
              a =
                0 === i && i == e
                  ? o.sInfoEmpty + o.sInfoPostFix
                  : 0 === i
                  ? o.sInfoEmpty + " " + o.sInfoFiltered + o.sInfoPostFix
                  : i == e
                  ? o.sInfo + o.sInfoPostFix
                  : o.sInfo + " " + o.sInfoFiltered + o.sInfoPostFix,
              o = ((a = this._macros(a, r, l, e, i)), o.fnInfoCallback),
              n =
                (o && (a = o.call(t.oInstance, t, r, l, e, i, a)),
                t.aanFeatures.i);
            if (void 0 !== n) {
              for (var h = 0, c = n.length; h < c; h++) d(n[h]).html(a);
              d(t.nTable).triggerHandler("info.dt");
            }
            d("div.dt-info", s.table().container()).each(function () {
              d(this).html(a), s.trigger("info", [s.settings()[0], this, a]);
            });
          }
        },
        _macros: function (t, s, o, e, i) {
          var r = this.s.dtApi,
            l = this.s.dt,
            a = l.fnFormatNumber;
          return t
            .replace(/_START_/g, a.call(l, s))
            .replace(/_END_/g, a.call(l, o))
            .replace(/_MAX_/g, a.call(l, e))
            .replace(/_TOTAL_/g, a.call(l, i))
            .replace(/_ENTRIES_/g, r.i18n("entries", ""))
            .replace(/_ENTRIES-MAX_/g, r.i18n("entries", "", e))
            .replace(/_ENTRIES-TOTAL_/g, r.i18n("entries", "", i));
        },
        _parseHeight: function (t) {
          var s,
            o,
            t = /^([+-]?(?:\d+(?:\.\d+)?|\.\d+))(px|em|rem|vh)$/.exec(t);
          return (
            (null !== t &&
              ((o = parseFloat(t[1])),
              "px" === (t = t[2])
                ? (s = o)
                : "vh" === t
                ? (s = (o / 100) * d(l).height())
                : "rem" === t
                ? (s = o * parseFloat(d(":root").css("font-size")))
                : "em" === t &&
                  (s = o * parseFloat(d("body").css("font-size"))),
              s)) ||
            0
          );
        },
        _scroll: function () {
          var t,
            s = this,
            o = this.s.heights,
            e = this.dom.scroller.scrollTop;
          this.s.skip ||
            this.s.ingnoreScroll ||
            (e !== this.s.lastScrollTop &&
              (this.s.dt.bFiltered || this.s.dt.bSorted
                ? (this.s.lastScrollTop = 0)
                : (clearTimeout(this.s.stateTO),
                  (this.s.stateTO = setTimeout(function () {
                    s.s.dtApi.state.save();
                  }, 250)),
                  (this.s.scrollType =
                    Math.abs(e - this.s.lastScrollTop) > o.viewport
                      ? "jump"
                      : "cont"),
                  (this.s.topRowFloat =
                    "cont" === this.s.scrollType
                      ? this.pixelsToRow(e, !1, !1)
                      : this._domain("physicalToVirtual", e) / o.row),
                  this.s.topRowFloat < 0 && (this.s.topRowFloat = 0),
                  this.s.forceReposition ||
                  e < this.s.redrawTop ||
                  e > this.s.redrawBottom
                    ? ((t = Math.ceil(
                        ((this.s.displayBuffer - 1) / 2) * this.s.viewportRows
                      )),
                      (t = parseInt(this.s.topRowFloat, 10) - t),
                      (this.s.forceReposition = !1),
                      t <= 0
                        ? (t = 0)
                        : t + this.s.dt._iDisplayLength >
                          this.s.dt.fnRecordsDisplay()
                        ? (t =
                            this.s.dt.fnRecordsDisplay() -
                            this.s.dt._iDisplayLength) < 0 && (t = 0)
                        : t % 2 != 0 && t++,
                      (this.s.targetTop = t) != this.s.dt._iDisplayStart &&
                        ((this.s.tableTop = d(this.s.dt.nTable).offset().top),
                        (this.s.tableBottom =
                          d(this.s.dt.nTable).height() + this.s.tableTop),
                        (t = function () {
                          (s.s.dt._iDisplayStart = s.s.targetTop),
                            s.s.dtApi.draw("page");
                        }),
                        this.s.dt.oFeatures.bServerSide
                          ? ((this.s.forceReposition = !0),
                            d(this.s.dt.nTable).triggerHandler(
                              "scroller-will-draw.dt"
                            ),
                            a.versionCheck("2")
                              ? s.s.dtApi.processing(!0)
                              : this.s.dt.oApi._fnProcessingDisplay(
                                  this.s.dt,
                                  !0
                                ),
                            clearTimeout(this.s.drawTO),
                            (this.s.drawTO = setTimeout(t, this.s.serverWait)))
                          : t()))
                    : (this.s.topRowFloat = this.pixelsToRow(e, !1, !0)),
                  this._info(),
                  (this.s.lastScrollTop = e),
                  this.s.stateSaveThrottle(),
                  "jump" === this.s.scrollType &&
                    this.s.mousedown &&
                    (this.s.labelVisible = !0),
                  this.s.labelVisible &&
                    ((t = (o.viewport - o.labelHeight - o.xbar) / o.scroll),
                    this.dom.label
                      .html(
                        this.s.dt.fnFormatNumber(
                          parseInt(this.s.topRowFloat, 10) + 1
                        )
                      )
                      .css("top", e + e * t)
                      .css("display", "block")))));
        },
        _scrollForce: function () {
          var t = this.s.heights;
          (t.virtual = t.row * this.s.dt.fnRecordsDisplay()),
            (t.scroll = t.virtual),
            1e6 < t.scroll && (t.scroll = 1e6),
            (this.dom.force.style.height =
              t.scroll > this.s.heights.row
                ? t.scroll + "px"
                : this.s.heights.row + "px");
        },
      }),
      (i.oDefaults = i.defaults =
        {
          boundaryScale: 0.5,
          displayBuffer: 9,
          rowHeight: "auto",
          serverWait: 200,
        }),
      (i.version = "2.4.0"),
      d(o).on("preInit.dt.dtscroller", function (t, s) {
        var o, e;
        "dt" === t.namespace &&
          ((t = s.oInit.scroller), (o = a.defaults.scroller), t || o) &&
          ((e = d.extend({}, t, o)), !1 !== t) &&
          new i(s, e);
      }),
      (d.fn.dataTable.Scroller = i),
      (d.fn.DataTable.Scroller = i),
      d.fn.dataTable.Api);
  return (
    t.register("scroller()", function () {
      return this;
    }),
    t.register("scroller().rowToPixels()", function (t, s, o) {
      var e = this.context;
      if (e.length && e[0].oScroller)
        return e[0].oScroller.rowToPixels(t, s, o);
    }),
    t.register("scroller().pixelsToRow()", function (t, s, o) {
      var e = this.context;
      if (e.length && e[0].oScroller)
        return e[0].oScroller.pixelsToRow(t, s, o);
    }),
    t.register(
      ["scroller().scrollToRow()", "scroller.toPosition()"],
      function (s, o) {
        return (
          this.iterator("table", function (t) {
            t.oScroller && t.oScroller.scrollToRow(s, o);
          }),
          this
        );
      }
    ),
    t.register("row().scrollTo()", function (o) {
      var e = this;
      return (
        this.iterator("row", function (t, s) {
          t.oScroller &&
            ((s = e
              .rows({ order: "applied", search: "applied" })
              .indexes()
              .indexOf(s)),
            t.oScroller.scrollToRow(s, o));
        }),
        this
      );
    }),
    t.register("scroller.measure()", function (s) {
      return (
        this.iterator("table", function (t) {
          t.oScroller && t.oScroller.measure(s);
        }),
        this
      );
    }),
    t.register("scroller.page()", function () {
      var t = this.context;
      if (t.length && t[0].oScroller) return t[0].oScroller.pageInfo();
    }),
    a
  );
});
