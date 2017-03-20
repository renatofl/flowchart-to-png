$(function () {
    var elementDataFlow = $('#' + idDataFlowchart);
    var DataFlow = elementDataFlow.val();
    if (DataFlow)
    {
        fFlowChart.loadData(DataFlow);
    } else {
        // inicio e fim
        fFlowChart.addStart({left: "20px", top: "180px"});
        fFlowChart.addEnd({left: "940px", top: "180px"});
    }
    $(htmlDataField).val($('#canvas').html());

    if (ParamsConfig.disabled) {
        $('#action,#condition').remove();
        return false;
    }

    elementDataFlow.parent('form').submit(function () {
        fFlowChart.setData();
    });

    var html = 'Descrição: <input type="text" id="descricao"/><br/>';
    $('#action').click(function () {
        $('#actionPopup').dialog("open");
    });
    $('#condition').click(function () {
        $('#conditionPopup').dialog("open");
    });

    $('#actionPopup').dialog({
        'autoOpen': false,
        buttons: {
            "Incluir": function () {
                var element = $(this);
                if (!fFlowChart.validationPopup(element))
                {
                    return false;
                }
                var data = {
                    text: element.find('#descricao').val(),
                    action: element.find('#action-select').val(),
                    extraParams: element.find('#extra-params:visible').val(),
                    countSource: returnsRelation[element.find('#action-select').val()].length
                };
                fFlowChart.addAction(data);
                $(this).dialog("close");
            },
            Fechar: function () {
                $(this).dialog("close");
            }
        }});
    $('#conditionPopup').dialog({
        'autoOpen': false,
        buttons: {
            "Incluir": function () {
                var element = $(this);
                if (!fFlowChart.validationPopup(element))
                {
                    return false;
                }
                var data = {
                    text: element.find('#descricao').val(),
                    condition: element.find('#condition-select').val(),
                    extraParams: element.find('#extra-params:visible').val(),
                };
                fFlowChart.addCondition(data);
                $(this).dialog("close");
            },
            Fechar: function () {
                $(this).dialog("close");
            }
        }});
    $('#relationPopup').dialog({
        'autoOpen': false,
        buttons: {
            "Incluir": function () {
                var element = $(this);
                if (!fFlowChart.validationPopup(element))
                {
                    return false;
                }
                connectionLast.getOverlay("label").setLabel('<label>' + element.find('#descricao').val() + '</label><input type="hidden" value="' + element.find('#relation-select').val() + '"/>');
                $(this).dialog("close");
            }
        }});
    $('#canvas').on("dblclick", "div.window", function () {
        var type = $(this).attr('type');
        if (type == 'start' || type == 'end') {
            return false;
        }
        if (typeof urlDetails  != 'undefined') {
            var params = "&action=" + $(this).attr('action') 
                    + "&extra-params=" + $(this).attr('extra-params')
                    + "&label=" + $(this).find('p').text();
            var parentCaller = this;
            $('#contentDetails').html("Aguarde, buscando informações...");
            $('#contentDetails').load(encodeURI(urlDetails + params));
            $('#detailsPopup').dialog({
                'autoOpen': true,
                buttons: {
                    Remover: function () {
                        if (confirm("Confirma a exclusão desse passo?")) {
                            instance.remove($(parentCaller).attr('id'));
                            $(this).dialog("close");
                        }
                    },
                    Fechar: function () {
                        $(this).dialog("close");
                    }
                }});
            
        }
    });
    $('#action-select, #condition-select').change(function () {
        $(this).parent().find('.extra-params').hide();
        $('#ep-' + $(this).val()).show();
    });
});

var fFlowChart = {
    addStart: function (params) {
        var container = $('#canvas');
        var paramsMerge = $.extend({
            left: '0px',
            top: '0px',
            id: 'flowchartInicio',
            text: 'Inicio'
        }, params);
        var elementHtml = '<div type="start" style="left: ' + paramsMerge.left + '; top: ' + paramsMerge.top + '" '
                + 'class="window jtk-node" '
                + 'data-shape="Circle" '
                + 'id="' + paramsMerge.id + '">'
                + '<div class="fc-inner-node"><p>' + paramsMerge.text + '</p></div>'
                + '</div>';
        container.append(elementHtml);
        _addEndpoints(paramsMerge.id, ["RightMiddle"], []);
    },
    addEnd: function (params) {
        var container = $('#canvas');
        var paramsMerge = $.extend({
            left: '0px',
            top: '0px',
            id: 'flowchartFim',
            text: 'Fim'
        }, params);
        var elementHtml = '<div type="end" style="left: ' + paramsMerge.left + '; top: ' + paramsMerge.top + '" '
                + 'class="window jtk-node" '
                + 'data-shape="Circle" '
                + 'id="' + paramsMerge.id + '">'
                + '<div class="fc-inner-node"><p>' + paramsMerge.text + '</p></div>'
                + '</div>';
        container.append(elementHtml);
        _addEndpoints(paramsMerge.id, [], ["LeftMiddle"]);
    },
    addAction: function (params) {
        var container = $('#canvas');
        var numElement = new Date().getTime();
        var paramsMerge = $.extend({
            left: '0px',
            top: '0px',
            id: 'flowchartWindow' + numElement,
            text: 'Ação ' + numElement,
            action: '',
            extraParams: '',
            countSource: 2
        }, params);
        var elementHtml = '<div type="action" style="left: ' + paramsMerge.left + '; top: ' + paramsMerge.top + '" '
                + 'class="window jtk-node" '
                + 'action="' + paramsMerge.action + '" '
                + 'extra-params="' + paramsMerge.extraParams + '" '
                + 'countSource="' + paramsMerge.countSource + '" '
                + 'id="' + paramsMerge.id + '">'
                + '<div class="fc-inner-node"><p>' + paramsMerge.text + '</p></div>'
                + '</div>';
        container.append(elementHtml);
        sources = ["RightMiddle", "BottomCenter"];
        _addEndpoints(paramsMerge.id, sources.slice(0, paramsMerge.countSource), ["TopCenter", "LeftMiddle"]);
    },
    addCondition: function (params) {
        var container = $('#canvas');
        var numElement = new Date().getTime();
        var paramsMerge = $.extend({
            left: '0px',
            top: '0px',
            id: 'flowchartWindow' + numElement,
            text: 'Decisão ' + numElement,
            action: '',
            extraParams: ''
        }, params);
        var elementHtml = '<div type="condition" style="left: ' + paramsMerge.left + '; top: ' + paramsMerge.top + '" '
                + 'class="window jtk-node" '
                + 'condition="' + paramsMerge.condition + '" '
                + 'extra-params="' + paramsMerge.extraParams + '" '
                + 'data-shape="Diamond" '
                + 'id="' + paramsMerge.id + '">'
                + '<div class="fc-inner-node"><p>' + paramsMerge.text + '</p></div>'
                + '</div>';
        container.append(elementHtml);
        _addEndpoints(paramsMerge.id, ["LeftMiddle", "RightMiddle"], ["BottomCenter"]);
    },
    getData: function () {
        var data = {'nodes': [], 'edges': []};
        var source, id, overlayLabel;
        $.each($('#canvas div.window'), function () {
            source = $(this);
            data.nodes.push({
                id: source.attr('id'),
                type: source.attr('type'),
                text: source.find('.fc-inner-node p').html(),
                left: source.css('left'),
                top: source.css('top'),
                action: source.attr('action'),
                extraParams: source.attr('extra-params'),
                countSource: source.attr('type') == 'action' ? source.attr('countSource') : null
            });

        });
        $.each(instance.getConnections(), function () {
            source = $(this.source);
            target = $(this.target);
            overlayLabel = this.getOverlay("label").getLabel();
            data.edges.push({
                'source': source.attr('id'),
                'target': target.attr('id'),
                'data': {
                    'label': $($(overlayLabel)[0]).text(),
                    'return': $($(overlayLabel)[1]).val(),
                    'positionSource': this.endpoints[0].anchor.type,
                    'positionTarget': this.endpoints[1].anchor.type,
                }
            });
        });
        return data;
    },
    setData: function () {
        $('#' + idDataFlowchart).val(JSON.stringify(fFlowChart.getData()));
        $(htmlDataField).val($('#canvas').html());
    },
    loadData: function (dataFlow) {
        var dataLoad = $.parseJSON(dataFlow);
        $.each(dataLoad.nodes, function () {
            switch (this.type)
            {
                case 'action':
                    fFlowChart.addAction(this);
                    break;
                case 'condition':
                    fFlowChart.addCondition(this);
                    break;
                case 'start':
                    fFlowChart.addStart(this);
                    break;
                case 'end':
                    fFlowChart.addEnd(this);
                    break;
            }
        });
        instance.unbind("connection");
        $.each(dataLoad.edges, function () {
            connection = this;
            instance.bind("connection", function (connInfo, originalEvent) {
                connInfo.connection.getOverlay("label").setLabel('<label>' + connection.data.label + '</label><input type="hidden" value="' + connection.data.return + '"/>');
            });
            instance.connect({uuids: [this.source + this.data.positionSource, this.target + this.data.positionTarget], editable: true});
            instance.unbind("connection");
        });
        instance.bind("connection", function (connInfo, originalEvent) {
            init(connInfo.connection);
        });
    },
    validationPopup: function (element) {
        element.find('span.flowchart-input-error').remove();
        var htmlError = '<span class="flowchart-input-error">Não pode ser vazio.</span>';
        var noErrors = true;
        $.each(element.find('select:visible,input:visible').not('input.select2-input'), function () {
            if ($(this).val() == '' || $(this).val() == null)
            {
                $(this).after(htmlError);
                noErrors = false;
            }
        });
        return noErrors;
    }
};
