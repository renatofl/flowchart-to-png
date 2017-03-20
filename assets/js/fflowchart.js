var connectionLast;
var instance = window.jsp = jsPlumb.getInstance({
    // default drag options
    DragOptions: { cursor: 'pointer', zIndex: 2000 },
    // the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
    // case it returns the 'labelText' member that we set on each connection in the 'init' method below.
    ConnectionOverlays: [
        [ "Arrow", {
            location: 1,
            visible:true,
            id:"ARROW"
        } ],
        [ "Label", {
            location: 0.1,
            id: "label",
            cssClass: "aLabel"
        }]
    ],
    Container: "canvas"
});
// the definition of source endpoints (the small blue ones)
var sourceEndpoint = {
    endpoint: "Dot",
    paintStyle: {
        strokeStyle: "#7AB02C",
        fillStyle: "transparent",
        radius: 7,
        lineWidth: 3
    },
    isSource: true,
    connector: [ "Flowchart", { stub: [40, 60], gap: 10, cornerRadius: 5, alwaysRespectStubs: true } ],
    dragOptions: {},
    overlays: [
        [ "Label", {
            location: [0.5, 1.5],
            label: "Drag",
            cssClass: "endpointSourceLabel",
            visible:false
        } ]
    ]
},
// the definition of target endpoints (will appear when the user drags a connection)
targetEndpoint = {
    endpoint: "Dot",
    paintStyle: { fillStyle: "#7AB02C", radius: 11 },
    maxConnections: -1,
    dropOptions: { hoverClass: "hover", activeClass: "active" },
    isTarget: true,
    overlays: [
        [ "Label", { location: [0.5, -0.5], label: "Drop", cssClass: "endpointTargetLabel", visible:false } ]
    ]
},
init = function (connection) {
    connectionLast = connection;

    if($(connection.source).attr('type') == 'condition')
    {
        if(connection.endpoints[0].anchor.type == 'LeftMiddle')
        {
            connection.getOverlay("label").setLabel('<label>Não</label><input type="hidden" value="false"/>');
        }else{
            connection.getOverlay("label").setLabel('<label>Sim</label><input type="hidden" value="true"/>');
        }
        return false;
    }
    var returns = returnsRelation[$(connection.source).attr('action')];
    var returnsLabel = returnsLabelRelation[$(connection.source).attr('action')];
    if(returns.length == 1)
    {
        connection.getOverlay("label").setLabel('<label>' + returnsLabel[0] + '</label><input type="hidden" value="' + returns[0] + '"/>');
        return false;
    }
    // verifica se já existe um relation criada
    var elements, indexSearch, returnsSelects = [], returnsLabelSelects = [];
    returnsSelects = $.extend([], returns);
    returnsLabelSelects = $.extend([], returnsLabel);
    instance.select({source:  $(connection.source).attr('id') }).each(function(connection) {
        elements = $(connection.getOverlay("label").getLabel());
        if(elements.length > 0)
        {
            indexSearch = returnsSelects.indexOf($(elements[1]).val());
            if (indexSearch > -1) {
                returnsSelects.splice(indexSearch, 1);
                returnsLabelSelects.splice(indexSearch, 1);
            }
        }
    });
    var htmlDescricao = '<label>Nome da relação:</label><input id="descricao" type="text" value="' + returnsLabelSelects[0] + '"/><br/>';
    var htmlSelect = '<label>Selecione a relação: </label><select id="relation-select">';
    $.each(returnsSelects, function(index,value){
        htmlSelect += '<option value="' + value + '">' + returnsLabelSelects[index] + '</option>';
    });
    htmlSelect += '</select>';
    if(connection.getOverlay("label").getLabel())
        return false;

    $('#relationPopup').dialog("open").html(htmlDescricao + htmlSelect);
};

var _addEndpoints = function (toId, sourceAnchors, targetAnchors) {
    var endPoint;
    for (var i = 0; i < sourceAnchors.length; i++) {
        var sourceUUID = toId + sourceAnchors[i];
        if(sourceAnchors[i] == 'LeftMiddle'){
                sourceEndpoint.paintStyle = {
                    strokeStyle: "#FF0000",
                    fillStyle: "transparent",
                    radius: 7,
                    lineWidth: 3
                };
        }else{
                sourceEndpoint.paintStyle = {
                    strokeStyle: "#7AB02C",
                    fillStyle: "transparent",
                    radius: 7,
                    lineWidth: 3
                };
        }
        endPoint = instance.addEndpoint(toId, sourceEndpoint, {
            anchor: sourceAnchors[i], uuid: sourceUUID
        });
        if(ParamsConfig.disabled)
        {
            endPoint.setEnabled(false);
            $('#flowchart-help').hide();
        }
    }
    for (var j = 0; j < targetAnchors.length; j++) {
        var targetUUID = toId + targetAnchors[j];
        instance.addEndpoint(toId, targetEndpoint, { anchor: targetAnchors[j], uuid: targetUUID });
    }

    // listen for new connections; initialise them the same way we initialise the connections at startup.
    instance.bind("connection", function (connInfo, originalEvent) {
        init(connInfo.connection);
    });

    if(!ParamsConfig.disabled){
        instance.draggable(jsPlumb.getSelector(".flowchart-demo .window"), { grid: [20, 20] });
    }
};