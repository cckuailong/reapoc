/*
* Author: PickPlugins
* Copyright:
*
* */
editorSettings = {
    activeTab: 0,
    selectedElement: {path:[], elType: '', data: []},
    breakpoints:{mobile:'576px', tablet:'992px', desktop:'1200px'},
    editElementData: [],
    elementTree:[],

}

tabNavs = document.querySelectorAll('.tab-navs .nav');
tabsContent = document.querySelectorAll('.tab-content');
toolsToggle = document.querySelectorAll('.tools-toggle');
templatePreview = document.getElementById('template-preview');
codeDisplay = document.getElementById('codeDisplay');
selectedObjectSettings = document.getElementById('selectedObjectSettings');
elementTreeWrap = document.getElementById('tree-list');
searchElement = document.getElementById('searchElement');
elementListWrap = document.getElementById('elementListWrap');
searchResults = document.getElementById('searchResults');



tools_tabs_switch(editorSettings);

function tools_tabs_switch(editorSettings){
    activeTab = editorSettings.activeTab;


    i = 0;
    tabNavs.forEach((tabNav) => {
        content = tabsContent[i]

        tabNav.classList.remove("active");
        tabNav.classList.remove("inactive");

        content.classList.remove("active");
        content.classList.remove("inactive");


        if(i == activeTab){

            tabNav.classList.add("active");
            content.classList.add("active");
            content.style.display = 'block';

        }else{
            tabNav.classList.add("inactive");
            content.classList.add("inactive");
            content.style.display = 'none';
        }
        i++;
    });

}

searchElement.addEventListener('keyup', () => {

    keyword = searchElement.value.toLowerCase();
    //console.log(typeof  keyword);
    results = [];

    if(keyword.length > 0){


        toolsToggle = document.querySelectorAll('#elementListWrap > .tools-toggle');
        toolsToggle.forEach((item) => {

            elementList = item.children[1].children[0];
            elements = elementList.children;

            //console.log(typeof elements);

            for(index in elements){
                element = elements[index];

                if(typeof(element.innerText) == 'string'){
                    elName = element.innerText.toLowerCase();

                    n = elName.indexOf(keyword);
                    if(n<0){

                    }else{

                        results.push(element);
                    }

                }

            }

        })


        for(i in results){

            item = results[i];

            searchResults.append(item);
        }

        //searchResults


        //console.log(results);

        searchResults.style.display = 'block';

    }else{

        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
    }






});

// Listen click event for tabs

tabNavs.forEach((nav) => {
    nav.addEventListener('click', () => {

        dataId = nav.getAttribute('data-id');
        data_id_nav = 'data-id-'+dataId;

        tabNavs.forEach((navItem) => {
            navClasses = navItem.className;
            navItem.classList.remove("active");

            nav.classList.add("active");
        })

        tabsContent.forEach((tabContent) => {
            tabContentClasses = tabContent.className;

            if(tabContentClasses.indexOf(data_id_nav) < 0){
                tabContent.style.display = 'none';
            }else{
                tabContent.style.display = 'block';
            }

        });

    });
});



toolsToggle.forEach((toggle) => {

    header = toggle.querySelectorAll('.toggle-header');


    header[0].addEventListener('click', () => {
        toggleClasses = toggle.className;

        if(toggleClasses.indexOf('active') < 0){

            toggle.classList.add("active");

        }else{

            toggle.classList.remove("active");
        }

    })
})





templateData = [
    {
        children: [
            {
                elType: "text",
                elName: "Text",
                class: "pglb-text pglb-element text p-1 m-1",
                id: "",
                isActive: false,
                innerHtml: "0 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                children: [],
            },

            {
                elType: "column",
                elName: "Column",
                class: "pglb-column col p-1 m-1",
                id: "",
                isActive: false,
                children: [
                    {
                        elType: "text",
                        elName: "Text",
                        class: "pglb-text pglb-element text p-1 m-1",
                        id: "",
                        isActive: false,
                        innerHtml: "20 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                        children: [],
                    },

                ],
            },
            {
                elType: "row",
                elName: "Row",
                class: "pglb-row row p-1 m-1",
                id: "",
                isActive: false,
                children: [
                    {
                        elType: "column",
                        elName: "Column",
                        class: "pglb-column col p-1 m-1",
                        id: "",
                        isActive: false,
                        children: [
                            {
                                elType: "text",
                                elName: "Text",
                                class: "pglb-text pglb-element text p-1 m-1",
                                id: "",
                                isActive: false,
                                innerHtml: "300 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                children: [],
                            },

                        ],
                    },
                    {
                        elType: "column",
                        elName: "Column",
                        class: "pglb-column col p-1 m-1",
                        id: "",
                        isActive: false,
                        children: [
                            {
                                elType: "text",
                                elName: "Text",
                                class: "pglb-text pglb-element text p-1 m-1",
                                id: "",
                                isActive: false,
                                innerHtml: "310 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                children: [],
                            }
                        ],
                    },
                    {
                        elType: "column",
                        elName: "Column",
                        class: "pglb-column col p-1 m-1",
                        id: "",
                        isActive: false,
                        children: [
                            {
                                elType: "text",
                                elName: "Text",
                                class: "pglb-text pglb-element text p-1 m-1",
                                id: "",
                                isActive: false,
                                innerHtml: "320 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                children: [],
                            }
                        ],
                    },


                ],
            },
            {
            elType: "container",
            class: "pglb-container container",
            id: "",
            elName: "Container",
            isActive: false,
            children: [
                {
                    elType: "row",
                    elName: "Row",
                    class: "pglb-row row p-1 m-1",
                    id: "",
                    isActive: false,
                    children: [
                        {
                            elType: "column",
                            elName: "Column",
                            class: "pglb-column col p-1 m-1",
                            id: "",
                            isActive: false,
                            children: [
                                {
                                    elType: "text",
                                    elName: "Text",
                                    class: "pglb-text pglb-element text p-1 m-1",
                                    id: "",
                                    isActive: false,
                                    innerHtml: "300 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                    children: [],
                                },

                            ],
                        },
                        {
                            elType: "column",
                            elName: "Column",
                            class: "pglb-column col p-1 m-1",
                            id: "",
                            isActive: false,
                            children: [
                                {
                                    elType: "text",
                                    elName: "Text",
                                    class: "pglb-text pglb-element text p-1 m-1",
                                    id: "",
                                    isActive: false,
                                    innerHtml: "310 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                    children: [],
                                }
                            ],
                        },
                        {
                            elType: "column",
                            elName: "Column",
                            class: "pglb-column col p-1 m-1",
                            id: "",
                            isActive: false,
                            children: [
                                {
                                    elType: "text",
                                    elName: "Text",
                                    class: "pglb-text pglb-element text p-1 m-1",
                                    id: "",
                                    isActive: false,
                                    innerHtml: "320 The paragraph element is the default element type.  It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.",
                                    children: [],
                                }
                            ],
                        },


                    ],
                },
            ],
            },
        ]
    }
]






elementsData = {
    container:{
        elType: "container",
        class: "pglb-container container",
        id: "",
        isActive: false,
        children: [],
    },
    row:{
        elType: "row",
        class: "pglb-row row p-1 m-1",
        id: "",
        isActive: false,
        children: [],
    },
    column:{
        elType: "column",
        class: "pglb-column col p-1 m-1",
        id: "",
        isActive: false,
        children: [],
    },
    heading:{
        elType: "heading",
        class: "pglb-heading pglb-element p-1 m-1",
        id: "",
        isActive: false,
        tag: "h2",
        innerHtml: 'The heading element',
        style:{
            color: {'576px': '', '992px': '', '1200px': ''},
            hoverColor: {'576px': '', '992px': '', '1200px': ''},
            fontSize: {'576px': '', '992px': '', '1200px': ''},
            fontFamily: {'576px': '', '992px': '', '1200px': ''},
            textAlign: {'576px': '', '992px': '', '1200px': ''},
            fontWeight: {'576px': '', '992px': '', '1200px': ''},
            textTransform: {'576px': '', '992px': '', '1200px': ''},
            textDecoration: {'576px': '', '992px': '', '1200px': ''},
            fontStyle: {'576px': '', '992px': '', '1200px': ''},
            lineHeight: {'576px': '', '992px': '', '1200px': ''},
            letterSpacing: {'576px': '', '992px': '', '1200px': ''},
            zIndex: {'576px': '', '992px': '', '1200px': ''},
            margin: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
            padding: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
        },
        children: [],
    },
    div:{
        elType: "div",
        class: "pglb-div pglb-element p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: 'Div element',
        children: [],
    },

    text:{
        elType: "text",
        class: "pglb-text pglb-element p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: 'The paragraph element is the default element type. It should not have any alignment of any kind. It should just flow like you would normally expect. Nothing fancy. Just straight up text, free flowing, with love.',
        style:{
            color: {'576px': '', '992px': '', '1200px': ''},
            hoverColor: {'576px': '', '992px': '', '1200px': ''},
            fontSize: {'576px': '', '992px': '', '1200px': ''},
            fontFamily: {'576px': '', '992px': '', '1200px': ''},
            textAlign: {'576px': '', '992px': '', '1200px': ''},
            fontWeight: {'576px': '', '992px': '', '1200px': ''},
            textTransform: {'576px': '', '992px': '', '1200px': ''},
            textDecoration: {'576px': '', '992px': '', '1200px': ''},
            fontStyle: {'576px': '', '992px': '', '1200px': ''},
            lineHeight: {'576px': '', '992px': '', '1200px': ''},
            letterSpacing: {'576px': '', '992px': '', '1200px': ''},
            zIndex: {'576px': '', '992px': '', '1200px': ''},
            margin: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
            padding: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
        },
        children: [],
    },

    emptyRow:{
        elType: "emptyRow",
        class: "pglb-empty text-center w-100 p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: '<i class="far fa-plus-square"></i> Add Row',
        children: [],
    },
    emptyColumn:{
        elType: "emptyColumn",
        class: "pglb-empty text-center w-100 p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: '<i class="far fa-plus-square"></i> Add Column',
        children: [],
    },

    empty:{
        elType: "empty",
        class: "pglb-empty text-center w-100 p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: '<i class="far fa-plus-square"></i> Add Elements',
        children: [],
    },

    link:{
        elType: "link",
        class: "pglb-link pglb-element p-1 m-1",
        id: "",
        isActive: false,
        innerHtml: 'Link text',
        target: '_blank',
        href: '#url',
        style:{
            color: {'576px': '', '992px': '', '1200px': ''},
            hoverColor: {'576px': '', '992px': '', '1200px': ''},
            fontSize: {'576px': '', '992px': '', '1200px': ''},
            fontFamily: {'576px': '', '992px': '', '1200px': ''},
            textAlign: {'576px': '', '992px': '', '1200px': ''},
            fontWeight: {'576px': '', '992px': '', '1200px': ''},
            textTransform: {'576px': '', '992px': '', '1200px': ''},
            textDecoration: {'576px': '', '992px': '', '1200px': ''},
            fontStyle: {'576px': '', '992px': '', '1200px': ''},
            lineHeight: {'576px': '', '992px': '', '1200px': ''},
            letterSpacing: {'576px': '', '992px': '', '1200px': ''},
            zIndex: {'576px': '', '992px': '', '1200px': ''},
            margin: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
            padding: {'576px': {top:'', right:'', bottom:'', left: ''}, '992px': {top:'', right:'', bottom:'', left: ''}, '1200px': {top:'', right:'', bottom:'', left: ''}},
        },
        children: [],
    },
    image:{
        elType: "image",
        class: "pglb-image pglb-element p-1 m-1",
        id: "",
        isActive: false,
        src: 'http://localhost/wp/wp-content/uploads/2018/11/Untitled-1.png',
        style:{
            width: {'576px': '', '992px': '', '1200px': ''},
            height: {'576px': '', '992px': '', '1200px': ''},
            margin: {'576px': '', '992px': '', '1200px': ''},
            padding: {'576px': '', '992px': '', '1200px': ''},
        },
        children: [],
    },


}

html = '';

function elementTree(templateData, elementTreeList) {

    count = 0;

    html += '<ul>';

    for (var index in templateData){

        element = templateData[index];
        elType = (element.elType) ? element.elType : '';
        elName = (element.elName) ? element.elName : '';

        children = element.children;
        html += '<li>';
        html += '<span>'+elName+'</span>';

        elementTreeList.push([index, elName]);

        //console.log(elementTreeList);



        if(children.length > 0){
            elementTree(children, elementTreeList);
        }
        html += '</li>';
    }
    html += '</ul>';

    //console.log(html);

    return html;

}


elementTreeList = [];

html = elementTree(templateData[0].children, elementTreeList);
//console.log(elementTreeList);
elementTreeWrap.innerHTML = html;


function elementTreeHTMl() {
    elementTree = editorSettings.elementTree;
    html = '';


    for(index in elementTree){
        elName = elementTree[index];

        html += '<li>';
        html += '<span>'+elName+'</span>';
        html += '</li>';
    }



    elementTreeWrap.getElementsByTagName('ul')[0].innerHTML = html;

}
//elementTreeHTMl();







function elTreeView(data) {


    selectedPath = (editorSettings.selectedElement.path.length != 0) ? editorSettings.selectedElement.path : [0];

    childrenMain = data[0].children;



    html = '';

    for (var index in childrenMain){
        element = childrenMain[index];
        elType = (element.elType) ? element.elType : '';
        element.index = index;
        element.id = elType+"-"+index;

        children = element.children;




        args = {};


        // console.log('#############: ');
        // console.log( [index]);
        // console.log('elType: '+ elType);
        // console.log( element );






        html += elementStartTag(element);

        if(children.length > 0){

            selectedPath.splice(0,1);
            args.selectedPath = selectedPath;


            generateChildHtml(children, args);
        }
        html += elementEndTag(element);


    }





    return html;


}


elTreeView(templateData);

function generateChildHtml(data, args){


    for (var index in data){
        element = data[index];
        elType = element.elType;
        id = element.id;

        selectedPath = args.selectedPath;
        children = element.children;

        element.id = (id) ? id : '';
        element.index = index;


        //childPath.push(index);
        // console.log('----#############: ');
        // console.log( [index]);
        // console.log('elType: '+ elType);
        // console.log( element );





        html += elementStartTag(element);

        if(  children.length > 0){

            generateChildHtml(children, args);
        }



        html += elementEndTag(element);
    }
}



templatePreview.innerHTML = html;



function  elementStartTag( element) {

    if(elType == 'container'){
        return generateElHtmlcontainer(element);
    }else if(elType == 'row'){
        return generateElHtmlrow(element);
    }
    else if(elType == 'column'){
        return generateElHtmlcolumn(element);
    }
    else if(elType == 'text'){
        return generateElHtmltext(element);
    }
    else if(elType == 'heading'){
        return generateElHtmlheading(element);
    }
    else if(elType == 'image'){
        return generateElHtmlimage(element);
    }
    else if(elType == 'link'){
        return generateElHtmllink(element);
    }
    else if(elType == 'empty'){
        return generateElHtmlempty(element);
    }
    else if(elType == 'emptyColumn'){
        return generateElHtmlemptyColumn(element);
    }
    else if(elType == 'emptyRow'){
        return generateElHtmlemptyRow(element);
    }
    else{
        return "";
    }
}


function  elementEndTag( element) {

    if(elType == 'container'){
        return '</div>';
    }else if(elType == 'row'){
        return '</div>';
    }
    else if(elType == 'column'){
        return '</div>';
    }
    else if(elType == 'text'){
        return '</div>';
    }
    else if(elType == 'heading'){
        return '</div>';
    }

    else if(elType == 'image'){
        return '</div>';
    }
    else if(elType == 'link'){
        return '</div>';
    }
    else if(elType == 'empty'){
        return '</div>';
    }
    else if(elType == 'emptyColumn'){
        return '</div>';
    }
    else if(elType == 'emptyRow'){
        return '</div>';
    }


    else{
        return "";
    }
}








function generateElHtmlcontainer(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;


    index = element.index;
    children = (children.length != 0) ? element.children : [elementsData.emptyRow];


    elData = {elType:elType,index:index,elId:elId,elClass: elClass };




    html += '<div index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div  class="containerSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement"><i class="fas fa-check"></i></span><span onclick="removeElement(this, event )" class="remove"><i class="fas fa-times"></i></span></div>';

    // html += '{{el_container}}';
    // html += '</div>';



    return html;

}


function generateElHtmlrow(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    index = element.index;



    children = (children.length != 0) ? element.children : [elementsData.emptyColumn];

    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="rowSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement"><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';

    // html += '{{el_row}}';
    // html += '</div>';



    return html;

}


function generateElHtmlcolumn(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;



    children = (children.length != 0) ? element.children : [elementsData.empty];




    index = element.index;

    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="columnSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement" ><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';

    if(children.length == 0){
        html += '<i class="far fa-plus-square"></i>';
    }

    // html += '{{el_column}}';
    // html += '</div>';

    return html;

}

function generateElHtmltext(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    innerHtml = element.innerHtml;
    index = element.index;



    children = element.children;

    html += '<div index="'+index+'"  id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="elementSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement" ><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';

    html += innerHtml;
    // html += '</div>';

    return html;
}


function generateElHtmlempty(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    innerHtml = element.innerHtml;
    index = element.index;

    children = element.children;



    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += innerHtml;
    // html += '</div>';

    return html;
}


function generateElHtmlemptyRow(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    innerHtml = element.innerHtml;
    index = element.index;

    children = element.children;



    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += innerHtml;
    // html += '</div>';

    return html;
}


function generateElHtmlemptyColumn(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    innerHtml = element.innerHtml;
    index = element.index;

    children = element.children;



    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += innerHtml;
    // html += '</div>';

    return html;
}









function generateElHtmllink(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    innerHtml = element.innerHtml;
    href = element.href;
    target = element.target;


    index = element.index;

    children = element.children;



    html += '<div index="'+index+'"  id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="elementSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement" ><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';

    html += '<a target="'+target+'" href="'+href+'">';
    html += innerHtml;
    html += '</a>';


    // html += '</div>';

    return html;

}
function generateElHtmlheading(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    tag = element.tag;
    innerHtml = element.innerHtml;
    index = element.index;

    children = element.children;





    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="elementSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement" ><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';
    html += '<'+tag+'>';
    html += innerHtml;
    html += '</'+tag+'>';
    // html += '</div>';

    return html;

}


function generateElHtmlimage(element){

    html = "";

    elId = element.id;
    elClass = element.class;
    elType = element.elType;
    index = element.index;
    src = element.src;




    html += '<div  index="'+index+'" id="'+elId+'" class="'+elClass+'" elType="'+elType+'">';
    html += '<div class="elementSettings"><span onclick="customizeElement(this, event)" class="customizeElement"><i class="far fa-edit"></i></span><span onclick="selectElement(this, event)" class="selectElement" ><i class="fas fa-check"></i></span><span onclick="removeElement(this, event)" class="remove"><i class="fas fa-times"></i></span></div>';
    html += '<img src="'+src+'" />';
    //html += '{{el_text}}';
    // html += '</div>';



    return html;

}






function removeElement(currentEl, event){



    newtemplateData = templateData;


    event.stopPropagation();

    el = currentEl;

    elId = el.getAttribute('id');
    elIndex = el.getAttribute('index');
    elClass = el.getAttribute('class');



    var els = [];
    var Indexes = [elIndex];


    while (el) {
        els.unshift(el);
        el = el.parentNode;

        elId = el.getAttribute('id');
        elIndex = el.getAttribute('index');
        elClass = el.getAttribute('class');



        if(elIndex !== null)
            Indexes.push(elIndex);


        if(elId == 'template-preview') break;



    }

    Indexes.reverse();


    var Indexes = Indexes.filter(function (item) {
        return item != null;
    });


    indexCount = Indexes.length;


    if(indexCount == 1){
        //delete templateData[Indexes[0]];
        newtemplateData.splice(Indexes[0], 1);
        templatePreview.innerHTML = elTreeView(newtemplateData);

    }else{


        containerIndex = Indexes[0];

        Indexes.splice(0, 1);




        newData = deletetemplateData(Indexes, newtemplateData[containerIndex]);
        templatePreview.innerHTML = elTreeView(newtemplateData);

        //
        // if(typeof newData !== 'undefined' && newData !== null){
        //
        //     templateData[containerIndex] = newData;
        // }

    }



}




function  deletetemplateData(index, data) {

    indexCount = index.length;


    if(indexCount > 1){

        newData = data.children[index[0]];

        //delete index[0];
        index.splice(0,1);



        data = deletetemplateData(index, newData);




    }else{


        data.children.splice(index[0], 1);



        return data;
    }




}

function generateElementSettings(elType) {



    html = '';


    html += 'Hello '+elType;



    selectedObjectSettings.innerHTML = html;

    toolsToggle = document.querySelectorAll('.tools-toggle');



}


function customizeElement(currentEl, event) {

    editorSettings.activeTab = 1;
    tools_tabs_switch(editorSettings);

    event.stopPropagation();
    el = currentEl;
    selectElement(currentEl, event);

    elType = editorSettings.selectedElement.elType;
    path = editorSettings.selectedElement.path;


    generateElementSettings(elType);


}




function getElementByIndex(templateData, index){
    //console.log(templateData);

    //console.log('indexes: ');
    //console.log(index);

    indexlength = index.length;
    //console.log('indexlength: '+ indexlength);

    if(indexlength == 1){

        //console.log('Retunred element');
        //console.log(templateData[index[0]]);
        //editorSettings.editElementData.data = templateData[index[0]];

        //console.log(editorSettings);

        return templateData[index[0]];


    }




    if(indexlength > 1){
        for (var x in index) {
            templateDataChild = templateData[index[x]].children;

            index.shift();
            getElementByIndex( templateDataChild, index);
        }
    }

}










function selectElement(currentEl, event){
    event.stopPropagation();

    el = currentEl;

    elId = el.getAttribute('id');
    elIndex = el.getAttribute('index');
    elClass = el.getAttribute('class');



    var els = [];
    var Indexes = [elIndex];


    while (el) {
        els.unshift(el);
        el = el.parentNode;

        elId = el.getAttribute('id');
        elIndex = el.getAttribute('index');
        elClass = el.getAttribute('class');

        elData = el.getAttribute('elData');

        console.log(elData);


        if(elIndex !== null)
            Indexes.push(elIndex);


        if(elId == 'template-preview') break;



    }

    Indexes.reverse();


    var Indexes = Indexes.filter(function (item) {
        return item != null;
    });


    currentEl.parentNode.parentNode.setAttribute("path", Indexes);
    elType = currentEl.parentNode.parentNode.getAttribute('elType');


    editorSettings.selectedElement.path = Indexes;
    editorSettings.selectedElement.elType = elType;



    //console.log('Main data: ');
    //console.log(templateData[0].children);

    selectedElementData = getElementByIndex( templateData[0].children, Indexes);


    console.log(selectedElementData);


}


function addElement(event, element ){

    event.stopPropagation();

    var elType = element.elType;


    selectedElement = editorSettings.selectedElement;


    selectedelType = (selectedElement.elType) ? selectedElement.elType : 'container';
    selectedPath = (selectedElement.path.length != 0) ? selectedElement.path : ["0"];

    if(selectedelType == 'container'){

        if(elType == 'container'){
            containerIndex = selectedPath[0];
            templateData.push(elementsData[elType]);

        }else if(elType == 'row'){
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];


            templateData[containerIndex].children.push(elementsData[elType]);


            //templateData.push(elementsData[elType]);
        }else if(elType == 'column'){
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];
            columnIndex = selectedPath[2];

            templateData[containerIndex].children[rowIndex].children.push(elementsData[elType]);

            //templateData.push(elementsData[elType]);
        }else {
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];
            columnIndex = selectedPath[2];
            index = selectedPath[3];

            templateData.push(elementsData[elType]);

        }




    }else if(selectedelType == 'row'){

        if(elType == 'column'){
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];
            columnIndex = selectedPath[2];

            templateData[containerIndex].children[rowIndex].children.push(elementsData[elType]);

            //templateData.push(elementsData[elType]);
        }

    }else if(selectedelType == 'column'){


        if(elType == 'container'){
            containerIndex = selectedPath[0];
            templateData.push(elementsData[elType]);

        }else if(elType == 'row'){
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];

            templateData[containerIndex].children.push(elementsData[elType]);


            //templateData.push(elementsData[elType]);
        }else if(elType == 'column'){
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];
            columnIndex = selectedPath[2];

            templateData[containerIndex].children[rowIndex].children.push(elementsData[elType]);

            //templateData.push(elementsData[elType]);
        }else {
            containerIndex = selectedPath[0];
            rowIndex = selectedPath[1];
            columnIndex = selectedPath[2];
            index = selectedPath[3];

            templateData[containerIndex].children[rowIndex].children[columnIndex].children.push(elementsData[elType]);


            //templateData.push(elementsData[elType]);

        }

    }else{

    }

    templatePreview.innerHTML = elTreeView(templateData);



}

