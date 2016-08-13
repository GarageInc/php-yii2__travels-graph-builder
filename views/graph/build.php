<?php

    use yii\helpers\Html;
    use yii\web\helpers;

    $this->title = 'Build Graph: ' . $graph->id . "(". $graph->graphname . ")";
    $this->params['breadcrumbs'][] = ['label' => 'My Graphs', 'url' => ['index']];
    $this->params['breadcrumbs'][] = 'Build';

    $css =<<< CSS
    
    .links .line_default {
      stroke: #999;
      stroke-opacity: 0.6;
    }
    
    .links .line_in_path {
      stroke: red;
      stroke-opacity: 0.6;
    }
        
    .links .line_selected {
      stroke: orange;
      stroke-opacity: 0.6;
    }
    
    .nodes .circle_default {
      fill: steelblue;
      stroke: #fff;
      stroke-width: 3px;
    }
    
    .nodes .circle_for_path {
          fill: red;
          stroke: #fff;
          stroke-width: 3px;
    }
    .nodes .circle_for_delete {
              fill: red;
              stroke: fiolet;
              stroke-width: 3px;
    }
CSS;

    $this->registerCss($css);

    $this->registerJsFile("https://d3js.org/d3.v4.js");

    $urlCreateNode = \yii\helpers\Url::to([
        'node/create'
    ]);


    $urlDeleteNode = \yii\helpers\Url::to([
        'node/delete'
    ]);

    $urlCreateEdge = \yii\helpers\Url::to([
        'edge/create'
    ]);


    $urlDeleteEdge = \yii\helpers\Url::to([
        'edge/delete'
    ]);

    $getGraphStructure = \yii\helpers\Url::to([
        'graph/structure'
    ]);

    $urlFindPath = \yii\helpers\Url::to([
        'graph/findpath'
    ]);


    $js = <<< JS
        
        var graph_id = $graph->id;
        
        var nodes = [];
        var edges = [];
        var edges_in_path = [];
        
        var selected_node_first = undefined;
        var selected_node_second = undefined;
        
        var selected_edge = undefined;
        var selected_node = undefined;
        
        var firstSelectBoxInPath = document.getElementById("firstNodeInPath");
        var secondSelectBoxInPath = document.getElementById("secondNodeInPath");
        
        
        var firstSelectBox = document.getElementById("firstNode");
        var secondSelectBox = document.getElementById("secondNode");
        
        loadData();
        invalidateDeleteEdgeButton();
                  
        function invalidateDeleteNodeButton(){
            
            if( selected_node){
                $("#bDeleteNode")[0].disabled = false
                
                var node = selected_node.datum();
                                
                $("#lDeletingNode")[0].innerHTML = node.nodename;                    
            }
        }
                  
        function invalidateDeleteEdgeButton(){
            
            if( selected_edge){
                $("#bDeleteEdge")[0].disabled = false
                
                var edgePath = [];
                
                var edge = selected_edge.datum();
                
                $("#lDeletingEdge")[0].innerHTML = edge.source.nodename + " <-> " + edge.target.nodename;                
            }
        }
        
        function nodeClick( d3node){
            
            selected_node = d3node;            
            selected_node.attr("class", "circle_for_delete")
            
            if( d3node === selected_node_first || d3node === selected_node_second){
                return;
            }
            
                        
            if( selected_node_first){
                selected_node_first.attr("class", "circle_default")
            }
            
            selected_node_first = selected_node_second;
            selected_node_second = d3node;
            
                
            if( selected_node_first){
            
                selected_node_first.attr("class",  "circle_for_path");    
                firstSelectBoxInPath.value = selected_node_first.datum().id;
            }
            
            if( selected_node_second){
                
                selected_node_second.attr("class",  "circle_for_delete");
                secondSelectBoxInPath.value = selected_node_second.datum().id;
            }
                        
            invalidateDeleteNodeButton();
            // invalidateDeleteEdgeButton();
        }
        
        function inPath(source, target){
                      
            for(var i=0; i < edges_in_path.length; i++){
                var edge = edges_in_path[i];
                                
                if(edge.first_node == source && edge.second_node == target || edge.first_node == target && edge.second_node == source  ){
                    
                    return true;
                }// pass
            }
            
            return false;
        }
        
        function generateDropDown(select){
                        
            select.innerHTML = "";
            
            for (var i = 0; i < nodes.length; i++){
                var opt = document.createElement('option');
                opt.value = nodes[i].id;
                opt.innerHTML = nodes[i].nodename;
                select.appendChild(opt);
            }
        }
        
        function generateDropDowns(){
            generateDropDown(firstSelectBox);
            generateDropDown(secondSelectBox);
            generateDropDown(firstSelectBoxInPath);
            generateDropDown(secondSelectBoxInPath );
        }
        
        function redraw(){
                
            var svg = d3.select("svg")
                width = +svg.attr("width"),
                height = +svg.attr("height");
                        
            svg.selectAll("*").remove();
            
            var color = d3.scaleOrdinal(d3.schemeCategory20);
            
            var simulation = d3.forceSimulation()
                .force("link", d3.forceLink().id(function(d) { return d.id; }))
                .force("charge", d3.forceManyBody())
                .force("center", d3.forceCenter(width / 2, height / 2))
                
            var maxWeigth = 0;
            

            edges.forEach(function(item){
                
                maxWeigth = item.weight > maxWeigth ? item.weight : maxWeigth                
            })
            
            var link = svg.append("g")
              .attr("class", "links")
                .selectAll("line")
                .data(edges)
                .enter().append("line")
              .attr("class", getLineClass)
              .attr("stroke-width", function(d) { 
                  
                  var stroke = 20.0 * d.weight / maxWeigth
                  return stroke < 5 ? 5 : stroke; 
              })
              .on("click", function(d){
                  
                  if( selected_edge){
                      selected_edge.attr("class", getLineClass)
                  }
                  
                  selected_edge = d3.select(this);
                                    
                  selected_edge.attr("class", "line_selected");
                  
                  invalidateDeleteEdgeButton();
              });
             
            function getLineClass(d){
                
                  if( inPath( d.source.id, d.target.id)){
                      
                      return "line_in_path"
                  }
                  
                  return "line_default";
            }
            
            var node = svg.append("g")
                      .attr("class", "nodes")
                        .selectAll("circle")
                        .data( nodes)                        
                        .enter()
                            .append("svg:g")
                                 .attr("class", "node")
                              .call(d3.drag()
                                  .on("start", dragstarted)
                                  .on("drag", dragged)
                                  .on("end", dragended)
                                  ); 
                         
            node.append("svg:circle")            
                    .attr("class", "circle_default")
                    .attr("font-size", "50px")
                    .attr("stroke","black")
                    .attr("x", function( d) { return d.x; })
                    .attr("y", function( d) { return d.y; })
                    .attr("r", 10)
                    .on("click", function(d, i, e){
                        nodeClick( d3.select(this))
                    })
                    
            
            node.append("svg:text")
                    .attr("class", "nodetext")
                    .attr("dx", 20)
                    .attr("dy", ".35em")
                    .text(function(d) { 
                        return d .nodename 
                    });           
             
                    
            simulation
              .nodes( nodes)
              .on("tick", ticked);
            
            var minDistane = 100;
            
            simulation.force("link")
              .links( edges)
              .distance( function(d){
                  
                  return d.weight > minDistane ? d.weight : minDistane;
              });
            
            function ticked() {
                link
                    .attr("x1", function(d) { return d.source.x; })
                    .attr("y1", function(d) { return d.source.y; })
                    .attr("x2", function(d) { return d.target.x; })
                    .attr("y2", function(d) { return d.target.y; });
                
                
                node
                    .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
            }
              
            function dragstarted(d) {
              if (!d3.event.active) 
                  simulation.alphaTarget(0.3).restart();
              d.fx = d.x;
              d.fy = d.y;
            }
            
            function dragged(d) {
              d.fx = d3.event.x;
              d.fy = d3.event.y;
            }
            
            function dragended(d) {
              if (!d3.event.active) 
                  simulation.alphaTarget(0);
              d.fx = null;
              d.fy = null;
            }
            
            function nodeClicked( d, i, e){
                
                console.log(d);
            }
        }
    
        function createNode( node_id, nodename){
            return {
                id: node_id,
                nodename: nodename
            }
        }
        
        function createEdge(id, weight, node_first_id, node_second_id){            
            
            return {
                id: id,
                weight: weight,
                source: node_first_id,
                target: node_second_id
            }
        }
        
        function tryDeleteEdge( edge_id){
            
            $.ajax({
                url: '$urlDeleteEdge',
                data: {
                  id: edge_id
                },
                success: function( result) {
                                        
                    loadData();
                },
                complete: function(){
                    $("#lDeletingEdge")[0].innerHTML = "";
                }
            })
        }
        
        function tryDeletNode( node_id){
            $.ajax({
                url: '$urlDeleteNode',
                data: {
                  id: node_id
                },
                success: function( result) {
                             
                    console.log(result)
                    loadData();
                },
                complete: function(){
                    $("#lDeletingNode")[0].innerHTML = "";
                }
            })
        }
        
        function tryAddEdge( graph_id, weight, node_first_id, node_second_id){
            
            if( node_first_id == node_second_id){
                alert("Нельзя связать вершину с самой собой!")
                return ;
            }
            
            $("#bAddEdge")[0].disabled = true;
            
            
            $.ajax({
                url: '$urlCreateEdge',
                type: 'POST',
                data: {
                  graph_id: graph_id,
                  weight: weight,
                  node_first_id: node_first_id,
                  node_second_id: node_second_id
                },
                success: function( new_id) {
                    
                    $("#edgeForm")[0].reset();
                    $("#bAddEdge")[0].disabled  = false;
                    
                    edges.push( createEdge(new_id, weight, node_first_id, node_second_id));
                    
                    redraw();
                }
            })
        }
        
        function tryAddNode( graph_id, nodename){
            
            $("#bAddNode")[0].disabled  = true;
            
            $.ajax({
                url: '$urlCreateNode',
                type: 'POST',
                data: {
                  graph_id: graph_id,
                  nodename: nodename
                },
                success: function( new_id) {
                    $("#nodeForm")[0].reset();
                    $("#bAddNode")[0].disabled  = false;
            
                    nodes.push( createNode( new_id, nodename));
                    
                    generateDropDowns()
                    redraw();
                }
            })
        }
    
        function tryFindPath(graph_id, node_first_id, node_second_id){
            $("#bFindPath")[0].disabled  = true;
            
            edges_in_path = [];
            
            $.ajax({
                url: '$urlFindPath',
                dataType: "json",
                data: {
                  graph_id: graph_id,
                  node_first_id: node_first_id,
                  node_second_id: node_second_id
                },
                success: function( result) {
                    
                    
                    $("#bFindPath")[0].disabled  = false;
                                
                    document.getElementById( "lLengthPath" ).innerText = result["length"]
                    
                    var path = result['path'].split("|")
                                        
                    var pathAlias = [];
                    
                    var pathCounter = 0
                    do {
                        edges_in_path.push({
                            first_node: path[pathCounter],
                            second_node: path[pathCounter+1]
                        })
                        
                        pathCounter++;
                        
                    } while ( pathCounter < path.length - 1)

                    for(var k=0; k < path.length; k++){
                        
                        for(var i=0; i < nodes.length; i++){
                           
                            if(path[k] == nodes[i].id){
                                pathAlias.push(nodes[i].nodename)
                            }
                        }
                    }
                    
                    document.getElementById( "lPath" ).innerText = pathAlias.join("->")
                    
                    redraw();
                    
                },
                error: function(){
                    $("#bFindPath")[0].disabled  = false;
                    alert("Божечки! Пути не существует, жизнь тлен, держитесь там")
                }
            })
            
        }
        
        function loadData(){
            
            $.ajax({
                    url: '$getGraphStructure',
                    dataType: "json",
                    data: {
                      graph_id: graph_id
                    },
                    success: function( result) {
                        var current_nodes = result['nodes'];
                        
                        nodes = current_nodes.map(function(item){
                          return createNode(
                                item.id,
                                item.nodename
                            )  
                        })
                        
                        var current_edges = result['edges'];
                        
                        edges = current_edges.map(function(item){
                          
                            return createEdge(
                                item.id,
                                item.weight,
                                item.node_first_id,
                                item.node_second_id
                            );
                        })
                        
                        generateDropDowns()
                        redraw();
                    }
                })
        }
                        
        function getSelectedNode(selectName){
                
            var e = document.getElementById( selectName );
            var id = e.options[e.selectedIndex].value;
             
            return id;
        }
        
        $("#nodeForm").submit(function(){
            var node_name = $("#nodename").val();
            
            tryAddNode(graph_id, node_name);
            
            return false;
        })
        
        
        $("#edgeForm").submit(function(e){
                     
            var weight = $("#edgeWeight").val()
             
            var firstNodeId = getSelectedNode("firstNode")
            var secondNodeId = getSelectedNode("secondNode")
            
            tryAddEdge( graph_id, weight, firstNodeId, secondNodeId)
            
            return false;
        })
        
        $("#findPathForm").submit(function(e){
                                   
            var firstNodeId = getSelectedNode("firstNodeInPath")
            var secondNodeId = getSelectedNode("secondNodeInPath")
            
            tryFindPath( graph_id, firstNodeId, secondNodeId)
            
            return false;
        })
        
        
        $("#edgeDeleteForm").submit(function(e){
                             
            tryDeleteEdge( selected_edge.datum().id)
            
            return false;
        })
        
        $("#nodeDeleteForm").submit(function(e){
                             
            tryDeletNode( selected_node.datum().id)
            
            return false;
        })

        

    
JS;


    $this->registerJs($js);

?>


<div class="graph-update">

    <h3><?= Html::encode($this->title)?></h3>


    <div  style="width:50%;padding: 10px; float: left;" >
        <form id="nodeForm" action="#">

            <div class="form-group">
                <input id="nodename" placeholder="Node name" class="form-control" type="text" maxlength="250">
            </div>

            <div class="form-group">
                <input type="submit" id="bAddNode" class="btn btn-success" value="Add node">
            </div>
        </form>
    </div>

    <div style="width:50%;padding: 10px; float: left;">
        <form id="edgeForm" action="#">

            <div class="form-group">
                <input id="edgeWeight" placeholder="Edge weight" class="form-control" type="number" maxlength="250" required>
            </div>

            <div class="form-group">
                <select class="form-control" id="firstNode">
                </select>
            </div>

            <div class="form-group">
                <select class="form-control" id="secondNode">
                </select>
            </div>

            <div class="form-group">
                <input type="submit" id="bAddEdge" class="btn btn-success" value="Add edge">
            </div>
        </form>
    </div>

    <div>
        <div style="padding: 10px; float: left;">
            <svg width="900" height="600" style="border: 1px solid; border-radius: 5px; "></svg>
        </div>

        <div  style="padding: 10px; float: left;">
            <form id="findPathForm" action="#">

                <div class="form-group">
                    <label class="form-control">Find path between:</label>
                </div>

                <div class="form-group">
                    <select class="form-control" id="firstNodeInPath">
                    </select>
                </div>

                <div class="form-group">
                    <select class="form-control" id="secondNodeInPath">
                    </select>
                </div>

                <div class="form-group">
                    <input type="submit" id="bFindPath" class="btn btn-success" value="Find path">
                </div>
            </form>
        </div>

        <div style="padding: 10px; float: left; width: 180px;">
            <div class="form-group">
                <label class="form-control" style="float: left;">Length:</label>
                <label  id="lLengthPath" style="float: left;"></label>
            </div>

            <div class="form-group">
                <label class="form-control"  style="float: left;">Path:</label>
                <label   style="float: left;" id="lPath">
                </label>
            </div>
        </div>

    </div>

    <div>
        <form id="edgeDeleteForm" action="#">
            <div style="padding: 10px; float: left; width: 180px;">
                <div class="form-group">
                    <input id="bDeleteEdge" type="submit" class="btn btn-danger"  value="Delete edge" disabled>
                    <label id="lDeletingEdge"></label>
                </div>
            </div>
        </form>
    </div>

    <div>
        <form id="nodeDeleteForm" action="#">
            <div style="padding: 10px; float: left; width: 180px;">
                <div class="form-group">
                    <input id="bDeleteNode" type="submit" class="btn btn-danger"  value="Delete node" disabled>
                    <label id="lDeletingNode"></label>
                </div>
            </div>
        </form>
    </div>
</div>