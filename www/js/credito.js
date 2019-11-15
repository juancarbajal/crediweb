//Modulo de Creditos
var Credito=function(){
    this.generarArbolCuotas=function(tr,arr){
        //tr Arbol
        Quipu.treeClear(tr);
        this.treeLimpiando=true;
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
}