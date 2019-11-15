const base_url=''; // '/quipu'
const XUL_NS = "http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"; 
//netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
//netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
/*************************************************************************************************/
String.prototype.trim = function() {
return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
return this.replace(/\s+$/,"");
}

var Quipu= new function(){
this.url='';
this.clave_primaria='COD';
this.campos=new Array();
this.campos_primarios=new Array();
this.url='';
this.list=null; //Listbox
this.tree=null; //Tree
this.ultimo_registro=null;
this.listLimpiando=false;
this.treeLimpiando=false;
this.foco=null;
this.defecto=null;
this.tiempoEspera=800;
this.tiempoBoton=2000;
this.setTree=function(tree){
this.tree=tree;
//this.tree.treeBoxObject.view=new nsTreeView();
}
this.nuevoRegistro= function(nuevos_campos){
  if (nuevos_campos!=null) this.campos=nuevos_campos;
  this.ultimo_registro=null;
  for(i=0;i<this.campos.length;i++){
    if ($(this.campos[i])!=null){
      if ((this.defecto!=null)&&(this.defecto[this.campos[i]]!=null)){
        $(this.campos[i]).value=this.defecto[this.campos[i]];
      }
      else{
        switch($(this.campos[i]).tagName){
        case 'textbox':$(this.campos[i]).value=''; break;
        case 'checkbox': $(this.campos[i]).checked=false ;break;
        case 'menulist': break;
        }
      }
    }
  }
  if (this.foco!=null) $(this.foco).focus();
  else $(this.campos[0]).focus();
}
this.guardarRegistro=function(params){
  new Ajax.Request(this.url,
  {
  parameters: params,              
      method: 'post',
      onSuccess: function(t){
      result=t.responseJSON;
      if (result.code==0){                     
        Quipu.alertError(result.msg);
        if ((result.data!=null) && (Quipu.tree!=null)){
          //Quipu.listAdd(Quipu.list,result.data);                        
          Quipu.treeAdd(Quipu.tree,result.data);
        } 
        if (Quipu.campos!=null)
          Quipu.nuevoRegistro();
      }
      else {
        Quipu.alertError(result.msg); 
      }
            
    }
  }
                   );
}
this.cargarLista=function(params){
  new Ajax.Request(
                   this.url,
  {parameters: params,
      method: 'post', 
      onSuccess: function(t){
      result=t.responseJSON;
      if (result.code==0){
        data=result.data;
        //Quipu.listAdd(Quipu.list,data);
        Quipu.treeAdd(Quipu.tree,data);
      }  
      else Quipu.alertError(result.msg); 
    }
  }
                   );
}
this.cargarUno=function(params){
  if (this.treeLimpiando==false){
    new Ajax.Request(
                     this.url,
  {parameters: params,
      method: 'post',
      onSuccess: function(t){
      result=t.responseJSON;
      if (result.code=='0'){
        data=result.data;
        //        Quipu.fromJSON(data[0]);
        //Quipu.ultimo_registro=data[0];
        Quipu.ultimo_registro=data[0];
        Quipu.fromJSON(data[0]);
      }
      else Quipu.alertError(t.responseJSON.msg);
    }
  }
                     );
  }
}
this.eliminarRegistro=function(params){
  new Ajax.Request(this.url,
  { parameters: params,
      onSuccess: function(t){
      result=t.responseJSON;
      if (result.code=='0'){
        Quipu.alertError(result.msg);
        if ((result.data!=null) && (Quipu.tree!=null)){
          //Quipu.listAdd(Quipu.list,result.data);
          Quipu.treeAdd(Quipu.tree,result.data);
        }
        if (Quipu.campos!=null){
          Quipu.nuevoRegistro();
        }
      }
      else {
        Quipu.alertError(result.msg);
      }
            
    } 
  }
                   )
}

this.toParams=function(){
  args=arguments;
  var hv=new Hash();
  for(i=0;i<args.length;i++){
    if (typeof(args[i])=='string'){
      e=$(args[i])
        if(e!=null){
          switch(e.tagName){
          case 'checkbox':
          if (e.checked==true)
            hv.set(e.id,e.getAttribute('value'));
          /*else
            hv.set(e.id,e.value);  */
          break;
          default:
          hv.set(e.id,e.value);  break;
          }
                        
        }
    }
    if (typeof(args[i])=='object'){
      keys=Object.keys(args[i]);
      values=Object.values(args[i]);
      for(j=0;j<keys.length;j++)
        hv.set(keys[j],values[j]);
    }
  }
  return hv.toQueryString();
}
this.fromJSON=function(js){
  keys=Object.keys(js);
  for(i=0;i<keys.length;i++){
      if(js[keys[i]]!=null)  {
          switch($(keys[i]).tagName){
          case 'checkbox': $(keys[i]).checked=(js[keys[i]]==1)?true:false; break;
          default: $(keys[i]).value=js[keys[i]];break;
          }
      }
      else{
          switch($(keys[i]).tagName){
          case 'textbox': case 'menulist':$(keys[i]).value='';break;
          case 'checkbox': $(keys[i]).checked=false; break;
          }
      }
      //else
      //  $(keys[i]).value='';
  }
  return js;
}
this.fromJSONVector=function(js){
    for(i=0;i<js.length;i++){
        keys=Object.keys(js[i]);
        //        if ( $( js[i][keys[0]] )!=null){
        $(js[i][keys[0]]).value=js[i][keys[1]];
        //alert(js[i][keys[0]]);
        //}
    }
}
this.listClear=function(lb){
  if(lb==null) lb=this.listBox;
  //lb.selectedIndex=-1; 
  while(lb.getRowCount()>0){
    lb.removeItemAt(lb.getRowCount()-1);
  }
}
this.listAdd=function(lb,arr,onCommand){ 
  this.listLimpiando=true;
  this.listClear(lb);
  for(i=0;i<arr.length;i++){
    values=Object.values(arr[i]);
    keys=Object.keys(arr[i]);
    var newListItem = document.createElementNS(XUL_NS, "listitem"); 
                                                
    var listCells=new Array();
    for(j=0;j<values.length;j++){//creamos las celdas            
      listCells.push(document.createElementNS(XUL_NS, "listcell")); 
      listCells[listCells.length-1].setAttribute('label',values[j]);
      newListItem.appendChild(listCells[j]);
    }
    newListItem.setAttribute('value',values[0]);
    lb.appendChild(newListItem);
  }
  this.listLimpiando=false;  
}
this.treeFindChildren=function (tree){
  child=tree.getElementsByTagNameNS(XUL_NS,'treechildren');
  return child.item(0);
}

this.treeClear=function(tree){
  var treeCh=this.treeFindChildren(tree);
  if (treeCh!=null){
    if( (tree.view!=null) && (tree.view.selection !=null)) {
      if (tree.currentIndex>-1){
        tree.view.selection.clearSelection();
      }
    }

    rows = treeCh.childNodes.length;
    for (var j=rows-1 ; j>=0 ; j--){
      treeCh.removeChild(treeCh.childNodes[j]);
    }
  }
}
this.treeDelete=function(tr){
  if (tr==null) tr=this.tree;
  if (tr.currentIndex!=-1){        
    selection = tr.contentView.getItemAtIndex(tr.currentIndex);
    var treeCh=this.treeFindChildren(tr);
    treeCh.removeChild(selection);
    return true
  }
  return false;
}
/*
  this.treeClear=function(tr){
  //si funciona en otra pc
  ch=this.treeFindChildren(tr);
  if (ch!=null) tr.removeChild(ch);
  var newTreeChildren = document.createElementNS(XUL_NS, "treechildren"); 
  tr.appendChild(newTreeChildren);
  }*/
/*
  this.treeClear=function(tr){
  if (tr==null) tr=Quipu.tree;
  ch=this.treeFindChildren(tr);
  if (ch!=null){ 
  tr.removeChild(ch);
  }

  }
*/
this._addFalse=function(treeCh){
  newTreeItem = document.createElementNS(XUL_NS, "treeitem"); 
  newTreeRow = document.createElementNS(XUL_NS, "treerow");
  newTreeRow.appendChild(document.createElementNS(XUL_NS, "treecell"));
  newTreeItem.appendChild(newTreeRow);
  treeCh.appendChild(newTreeItem);
}
this.treeAdd=function(tr,arr,onCommand){
  this.treeLimpiando=true;
  this.treeClear(tr);
  for(i=0;i<arr.length;i++){
    values=Object.values(arr[i]);
    keys=Object.keys(arr[i]);
    newTreeItem = document.createElementNS(XUL_NS, "treeitem"); 
    newTreeRow = document.createElementNS(XUL_NS, "treerow"); 
    treeCells=new Array();
    for(j=0;j<values.length;j++){//creamos las celdas            
      treeCells.push(document.createElementNS(XUL_NS, "treecell")); 
      treeCells[treeCells.length-1].setAttribute('label',values[j]);
      newTreeRow.appendChild(treeCells[j]);
    }
    newTreeItem.appendChild(newTreeRow);
    //newTreeItem.setAttribute('value',values[0]);

    child=this.treeFindChildren(tr);
    //alert(child);
    if (child!=null){
      child.appendChild(newTreeItem);
    }
    else{
      newTreeChildren = document.createElementNS(XUL_NS, "treechildren"); 
      newTreeChildren.appendChild(newTreeItem);
      tr.appendChild(newTreeChildren);
    }
                                                                                       
  }
  this.treeLimpiando=false;  
}
this.getTreeRow=function(tr){
  if (tr==null) tr=this.tree;
  if (tr.currentIndex!=-1){        
    selection = tr.contentView.getItemAtIndex(tr.currentIndex);
    return selection.firstChild;
  }
  return null;                                                                         
}
this.getTreeValue=function(tr){
  if (tr==null) tr=this.tree;
  if (tr.currentIndex!=-1){        
    selection = tr.contentView.getItemAtIndex(tr.currentIndex);
    return selection.firstChild.firstChild.getAttribute("label");
  }
  return null;
}
this.alertError=function(msg){ 
  alert(msg);
}
this.print=function(url,name,abase_url){
  if (name==null) name='_blank';
  if (abase_url == null) abase_url=base_url;
  print_url=abase_url+'/imprimir?pagina='+url;
  return window.open(print_url,name,'width=700;height=500px');
}
this.open=function(url,name,params){
  if (name==null) name='_blank';
  if (params==null) params="height=400,width=700";
  win=window.open(url,name,params);
  return win;
}
this.debugMsg=function(id,oldval,newval){
  alert(oldval + ' -> '+newval);
}
this.debugStart=function(variable){
  watch(variable,Quipu.debugMsg);
}
this.debugStop=function(variable){
  unwatch(variable);
}
this.desabilitaBoton=function(nombre,est){
    if (est==null) est=true;
    //true para activo
    //false para inactivo
    if ($(nombre)!=null){
        $(nombre).disabled=est;
        if (est==true){
            setTimeout('Quipu.desabilitaBoton("'+nombre+'",false)',this.tiempoBoton);
        }
    }
}
this.format=function(expr,decplaces){		
        return (Math.round(eval(expr)*Math.pow(10,decplaces))/Math.pow(10,decplaces));	  
} 
};

function lpad(str1,size,str2){
    for(i=str1.toString().length;i<size;i++){
        str1=str2+str1.toString();
    }
    return str1;
}
