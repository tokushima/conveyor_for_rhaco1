Ext.override(Ext.dd.DDProxy, {
    startDrag: function(x, y){
        this.y = y;
    },
    onDragOver: function(e, targetId){
        var target = Ext.get(targetId);
        this.outOfBound = false;
        if (target.hasClass('x-fieldset')) {
            this.lastTarget = target;
        }
    },
    onDragOut: function(e, targetId){
        this.outOfBound = true;
    },
    endDrag: function(){
        var dragEl = Ext.get(this.getDragEl());
        var el = Ext.get(this.getEl());
        if (this.outOfBound) {
            if (Ext.select('fieldset').getCount() > 1) {//削除条件をつける
                Manage.removeField(this.id);
            }
        }
        else 
            if (this.lastTarget) {
                if (this.y < Ext.get(this.lastTarget).getY()) {
                    Ext.get(el).insertAfter(this.lastTarget);
                    el.applyStyles({
                        position: '',
                        width: ''
                    });
                }
                else {
                    Ext.get(el).insertBefore(this.lastTarget);
                    el.applyStyles({
                        position: '',
                        width: ''
                    });
                }
            }
        if ('function' === typeof this.config.fn) {
            this.config.fn.apply(this.config.scope || window, [this, this.config.dragData]);
        }
    },
    setWorker: function(id){
        this.addInvalidHandleType('input');
        this.addInvalidHandleType('select');
        this.addInvalidHandleType('textarea');
    }
});

Ext.override(Ext.Panel.DD, {
    endDrag: function(e){
        var srcEl = Ext.get(this.getEl());
        var targetEl = Ext.get('bd');
        var x = e.getPageX();
        if (x > targetEl.getX() && x < targetEl.getX() + targetEl.getWidth()) {
            var y = e.getPageY();
            Ext.select('fieldset').each(function(item, elem, index){
                if (y > item.getY() && y < item.getY() + item.getHeight()) {
                    var newfield = Manage.addField(index + 1);
                    var srcContent;
                    if (Ext.isIE6 || Ext.isIE7) {
                        srcContent = srcEl.dom.firstChild.innerText;
                    }
                    else {
                        var srcContent = srcEl.dom.firstChild.textContent;
                    }
                    Manage.setConfig(newfield, srcContent);//タイトル部分に相当
                }
            });
        }
    }
});

var Manage = {
    init: function(){
        this.centerTabs = [new Ext.Panel({
            el: 'main',
            title: 'Line',
            frame: false,
            closable: false,
            bodyStyle: 'position:relative;',
            autoScroll: true
        }), new Ext.Panel({
            el: 'employee',
            title: 'Employed workers',
            frame: false,
            closable: false,
            autoScroll: true
        }), new Ext.Panel({
            el: 'workers',
            title: 'All workers',
            frame: false,
            closable: false,
            autoScroll: true
        })];
        this.layout = new Ext.Viewport({
            layout: "border",
            items: [{
                region: 'north',
                el: 'hd',
                html: '<h1 class="x-panel-header"><img src="' + logo + '" height="33px"/></h1>',
                split: false,//分割線
                autoHeight: true,
                minHeight: 0, //最小サイズ
                margines: '0 0 5 0',
                collapsible: false
            }, {
                region: 'east',
                title: 'Workers',
                el: 'nav',
                split: true,
                autoScroll: true,
                width: 300,
                minSize: 170,
                maxSize: 300,
                collapsible: true
            }, {
                region: 'south',
                el: 'ft',
                html: '<h3 class="x-panel-header">Copyright 2006- <a href=\"http://www.rhaco.org/\" target=\"blank\" style=\"color:#FFFFFF;\">The Rhacophorus Project</a>. All rights reserved.</h3>',//タイトル
                split: false,
                minHeight: 0,
                autoHeight: true,
                collapsible: false
            }, {
                region: 'center',
                el: 'bd',
                xtype: 'tabpanel',
                activeTab: 0,
                tabPosition: "bottom",
                autoScroll: true,
                resizeTabs: true,
                minTabWidth: 50,
                preferredTabWidth: 150,
                items: this.centerTabs
            }]
        });
        //hack for activate
        var center = this.layout.find('region', 'center');
        for (i = 0; i < this.centerTabs.length; i++) {
            center[0].activate(this.centerTabs[i]);
        }
        center[0].activate(this.centerTabs[0]);
        this.unmask.defer(100);
        
        this.modules = new Ext.data.Store({
            autoLoad: true,
            url: 'index.php?action=loadModule',
            reader: new Ext.data.JsonReader({
                root: 'modules'
            }, Ext.data.Record.create([{
                name: 'type'
            }, {
                name: 'name'
            }, {
                name: 'value'
            }, {
                name: 'description'
            }, {
                name: 'config'
            }]))
        });
        this.modules.on('load', function(a){
            this.setComboConfig(a);
            this.clearAccordion();
            this.setAccordion(a);
            this.clearLine();
            this.setLine();
            this.clearEmployee();
            this.setEmployee();
            
            this.layout.doLayout();
        }, this);
        this.loadedline = new Ext.data.Store({
            method: 'POST',
            url: 'index.php?action=load',
            reader: new Ext.data.JsonReader({
                root: 'plugins'
            }, Ext.data.Record.create([{
                name: 'module'
            }, {
                name: 'config'
            }]))
        });
        this.loadedline.on('load', function(a){
            this.loadLine(a);
        }, this);
        
        this.publishedline = new Ext.data.Store({
//        	proxy: new Ext.data.HttpProxy({method:'GET',url:'index.php?action=loadPublish'}),
            url: 'index.php?action=loadPublish',
            reader: new Ext.data.JsonReader({
                root: 'lines'
            }, Ext.data.Record.create([{
                name: 'linename'
            }]))
        });
        
        for (i = 0; i < this.centerTabs.length; i++) {
            this.centerTabs[i].on('activate', function(tab){
                tab.doLayout()
            });
        }
        this.centerTabs[2].on('activate', function(tab){
            if (!this.candidates) {
                this.setCandidates();
                tab.doLayout();
            }
        }, this);
    },
    setComboConfig: function(store){
        store.each(function(item){
            var config = item.data.config;
            for (key in config) {
                if (config[key].xtype == 'combo') {
                    config[key].store = new Ext.data.SimpleStore({
                        fields: ['value', 'text'],
                        data: config[key].value
                    });
                    config[key].value = config[key].value[0][1];
                    config[key].mode = 'local';
                    config[key].valueField = 'value';
                    config[key].displayField = 'text';
                    config[key].triggerAction = 'all';
                }
            }
        });
        
    },
    setAccordion: function(store){
        var east = this.layout.find('region', 'east')[0];
        this.toolbar = new Ext.Toolbar({
            applyTo: 'toolbar'
        });//createElement('strong') textContent = Search
        this.toolbar.add('Search');
        this.toolbar.add(new Ext.form.TextField({
            id: 'find-field',
            msgTarget: 'side',
            autoCreate: {
                tag: 'input',
                type: 'text',
                size: 15
            }
        }));
        Ext.get('find-field').on('keyup', function(e, el){
            this.searchWorkers(e, el, this.acc, 'title')
        }, this, {
            buffer: 150
        });
        east.add(this.toolbar);
        this.acc = new Ext.Panel({
            layout: 'accordion',
            title: '',
            layoutConfig: {
                titleCollapse: false,
				autoHeight:true,
                animate: true,
                shadow: true,
                frame: true,
                activeOnTop: false
            }
        });
        store.each(function(data){
            this.acc.add(new Ext.Panel({
                cls: data.data.type,
                title: data.data.name,
				autoHeight:true,
                html: '<p>' + data.data.description + '</p>',
                border: false,
                draggable: true
            }));
        }, this);
        east.add(this.acc);
		east.doLayout();
    },
    clearAccordion: function(){
        if (this.acc) {
            var east = this.layout.find('region', 'east')[0];
            east.remove(this.toolbar);
            east.remove(this.acc);
        }
    },
    setLine: function(){
        this.form = new Ext.form.FormPanel({
            labelWidth: 75,
            layout: 'fit',
            title: 'Line',
            header: false,
            bodyStyle: 'padding:15px',
            labelPad: 10,
            defaultType: 'fieldset',
            defaults: {
                msgTarget: 'side'
            },
            layoutConfig: {
                labelSeparator: ''
            },
            buttons: [{
                text: 'Add',
                handler: function(){
                    this.addField()
                },
                scope: this
            }, {
                text: 'Remove',
                handler: function(){
                    this.removeField()
                },
                scope: this
            }, {
                text: 'Generate',
                handler: function(){
                    this.form.getForm().submit({
                        url: 'index.php',
                        scope: this,
                        failure: function(form, action){
                            Ext.MessageBox.alert('Status', 'Generation failed.');
                        },//+error message
                        success: function(form, action){
                            Ext.MessageBox.alert('Status', 'Line generated successfully.');
                        }
                    });
                },
                scope: this
            }]
        });
        var field = new Ext.form.FieldSet({
            title: 'Line',
            width: 550,
            defaultType: 'textfield',
            autoHeight: true,
            autoWidth: true,
            defaults: {
                autoWidth: true,
                autoHeight: true,
            },
            items: [{
                mode:'remote',
                fieldLabel: 'Name',
                name: 'actionname',
                allowBlank: false,
                minChars:10000,
                xtype: 'combo',
                store: this.publishedline,
                valueField: 'linename',
                displayField:'linename',
                loadingText:'Searching...',
                onTriggerClick:function(){
                	this.store.reload();
                	this.bindStore(this.store,true);
			        if(this.disabled){
			            return;
			        }
			        if(this.isExpanded()){
			            this.collapse();
			            this.el.focus();
			        }else {
			            this.onFocus({});
			            if(this.triggerAction == 'all') {
			                this.doQuery(this.allQuery, true);
			            } else {
			                this.doQuery(this.getRawValue());
			            }
			            this.el.focus();
			        }
                 },
                scope:this
            }],
            buttons: [{
                text: 'Load',
                handler: function(){
                    this.loadedline.load({
                        params: this.form.getForm().getValues()
                    });
                },
                scope: this
            }]
        });
        //optionを変化した時の挙動と変化を記述
        //		var combo = field.find('xtype','combo');
        //		combo[0].on('select',function(cb, record, index){this.setConfig(field,record.data.text)},this);
        this.form.add(field);
        this.addField(0);
        this.centerTabs[0].add(this.form);
    },
    clearLine: function(){
        if (this.form) {
            this.centerTabs[0].remove(this.form);
        }
    },
    addField: function(no){
        var optionArray = [];
        this.modules.each(function(data){
            optionArray.push([data.data.value, data.data.name]);
        });
        var options = new Ext.data.SimpleStore({
            fields: ['path', 'text'],
            data: optionArray
        });
        var field = new Ext.form.FieldSet({
            title: 'Worker',
            width: 550,
            cls: 'worker',
            defaultType: 'textfield',
            autoHeight: true,
            autoWidth: true,
            defaults: {
                width: 400
            },
            items: [{
                mode: 'local',
                fieldLabel: 'Name',
                allowBlank: true,
                autoWidth: true,
                xtype: 'combo',
                store: options,
                displayField: 'text',
                valueField: 'path',
                hiddenName: 'usemodule[]',
                triggerAction: 'all'
            }]
        });
        var combo = field.find('xtype', 'combo');
        combo[0].on('select', function(cb, record, index){
            this.setConfig(field, record.data.text)
        }, this);
        if (no) {
            this.form.insert(no, field);
        }
        else {
            this.form.add(field);
        }
        this.form.doLayout();
        this.setDD();
        return field;
    },
    removeField: function(field){
        if (this.form.items.getCount() > 2) {
            if (field) {
                this.form.remove(field, true);
            }
            else {
                var lastfield = Ext.select('fieldset[class*=worker]').last();
                this.form.remove(lastfield.dom.id);
            }
            this.layout.doLayout();
            return true;
        }
        else {
            return false;
        }
    },
    setConfig: function(field, name, overwriteConfig){
        if (Ext.isSafari) {
            name = name.replace(/^[^a-zA-Z.0-9]+|[^a-zA-Z.0-9]+$/g, '');
        }
        var index = this.modules.find('name', name.trim());
        var childFields = field.findByType('fieldset');
        for (i = 0; i < childFields.length; i++) {
            childFields[i].destroy();
        }
        var combo = field.find('xtype', 'combo');
        combo[0].setValue(name.trim() + '.' + name.trim());
        var item = this.modules.getAt(index);
        var config = item.data.config;
        for (i = 0; i < config.length; i++) {
            if(config[i].xtype=='password'){
            	config[i].xtype = 'textfield';
            	config[i].inputType = 'password';
            }else if(config[i].xtype=='combo'){
            	var store0 = config[i].store.getAt(0);
            	config[i].value = store0.get('value');
            }
            if (overwriteConfig && overwriteConfig[config[i].key]) {
                config[i].value = overwriteConfig[config[i].key];
            }
           config[i].hiddenName= config[i].name;
        }
		if(config[0] === undefined) config = undefined;//TODO: not smart
			field.add(new Ext.form.FieldSet({
				title: 'Configs',
				width: 550,
				defaultType: 'textfield',
				autoHeight: true,
				autoWidth: true,
				defaults: {
					autoWidth: true
				},
				items: config
			}));
        this.form.doLayout();
    },
    setDD: function(){
        var dz = new Ext.dd.DropTarget('bd', {
            ddGroup: 'group'
        });
        Ext.select('fieldset[class*=worker]').each(function(e){
            var id = e.dom.id;
            e.dd = new Ext.dd.DDProxy(id, 'group');
            e.dd.setWorker(id);
        });
    },
    loadLine: function(a){
        do {
            var result = this.removeField();
        }
        while (result);
        var lastfield = Ext.select('fieldset[class*=worker]').last();
        this.form.remove(lastfield.dom.id);
        this.loadedline.each(function(item){
            var field = this.addField();
            this.setConfig(field, item.data.module, item.data.config);
        }, this);
        
    },
    
    
    unmask: function(){
        var mask = Ext.get('loading-mask');
        var msg = Ext.get('loading');
        if (mask && msg) {
            mask.shift({
                x: msg.getX(),
                y: msg.getY(),
                width: msg.getWidth(),
                height: msg.getHeight(),
                remove: true,
                duration: 1.6,
                opacity: 0.3,
                easing: 'bounceOut',
                callback: function(){
                    Ext.fly(msg).remove();
                }
            });
        }
    },
    
    
    searchWorkers: function(e, el, target, property){
        if (e.isSpecialKey() || e.isNavKeyPress()) {
            return false;
        }
        var val = el.value;
        if (val.length < 3 && val.length > 0) {
            return;
        }
        var children = target.findBy(function(){
            return true
        });
        if (0 === val.length) {
            for (i = 0; i < children.length; i++) {
                if (children[i].container.up('div.x-form-item')) {
                    if (target.title == 'Employed workers') {
                        this.clearEmployee();
                        this.setEmployee();
                    }
                    if (target.title == 'All workers') {
                        this.clearAllWorkers();
                        this.setAllWorkers();
                    }
                    return true;
                }
                else {
                    children[i].show();
                }
            }
            return true;
        }
        var regex = new RegExp('.*' + val + '.*', 'i');
        for (i = 0; i < children.length; i++) {
            if (children[i][property] && !children[i][property].match(regex)) {
                if (children[i].container.up('div.x-form-item')) {
                    children[i].container.up('div.x-form-item').remove();
                }
                else {
                    children[i].hide();
                }
            }
        }
    },
    
    
    setEmployee: function(){
        var items = [];
        this.modules.each(function(data, index){
            items[index] = {
                boxLabel: '<span class="' + data.data.type + '">' + data.data.name + '</span><br />' + data.data.description,
                name: 'enclosure[]',
                inputValue: data.data.value,
                autoHeight: true,
                autoWidth: true,
                xtype: 'checkbox'
            };
        });
        items.unshift({
            xtype: 'textfield',
            id: 'find-employee',
            fieldLabel: 'Search',
            msgTarget: 'side',
            autoHeight: true,
            autoWidth: true,
            autoCreate: {
                tag: 'input',
                type: 'text',
                size: 15
            }
        });
        this.employee = new Ext.form.FormPanel({
            labelWidth: 75,
            layout: 'fit',
            title: 'Employed workers',
            header: false,
            bodyStyle: 'padding:15px',
            labelPad: 10,
            defaultType: 'fieldset',
            defaults: {
                msgTarget: 'side'
            },
            layoutConfig: {
                labelSeparator: ''
            },
            items: [{
                title: 'Employed Workers',
                width: 550,
                cls: 'employee',
                defaultType: 'checkbox',
                autoHeight: true,
                autoWidth: true,
                defaults: {
                    width: 400
                },
                items: items
            }],
            buttons: [{
                text: 'Update',
                handler: function(){
                    Ext.MessageBox.confirm('Confirm', 'Are you sure you want to update?', function(btn){
                        if (btn == 'yes') {
                            this.employee.getForm().submit({
                                url: 'updator.php',
                                scope: this,
                                params: {
                                    'action': 'update'
                                },
                                failure: function(form, action){
                                    Ext.MessageBox.alert('Status', 'Update failed.');
                                },//+error message
                                success: function(form, action){
                                    this.modules.reload();
                                    Ext.MessageBox.alert('Status', 'Workers updated successfully.');
                                }
                            });
                        }
                    }, this);
                },
                scope: this
            }, {
                text: 'Remove',
                handler: function(){
                    Ext.MessageBox.confirm('Confirm', 'Are you sure you want to remove?', function(btn){
                        if (btn == 'yes') {
                            this.employee.getForm().submit({
                                url: 'updator.php',
                                scope: this,
                                params: {
                                    'action': 'remove'
                                },
                                failure: function(form, action){
                                    Ext.MessageBox.alert('Status', 'Remove failed.');
                                },//+error message
                                success: function(form, action){
                                    this.modules.reload();
                                    Ext.MessageBox.alert('Status', 'Workers removed successfully.');
                                }
                            });
                        }
                    }, this);
                },
                scope: this
            }]
        });
        this.centerTabs[1].add(this.employee);
        this.centerTabs[1].doLayout();
        Ext.get('find-employee').on('keyup', function(e, el){
            this.searchWorkers(e, el, this.employee, 'inputValue')
        }, this, {
            buffer: 150
        });
    },
    clearEmployee: function(){
        if (this.employee) {
            this.centerTabs[1].remove(this.employee);
        }
    },
    
    setCandidates: function(){
        this.candidates = new Ext.data.Store({
            autoLoad: true,
            url: 'updator.php?action=install',
            reader: new Ext.data.JsonReader({
                root: 'modules'
            }, Ext.data.Record.create([{
                name: 'type'
            }, {
                name: 'name'
            }, {
                name: 'description'
            }, {
                name: 'time'
            }, {
                name: 'value'
            }]))
        });
        this.candidates.on('load', function(a){
            this.clearAllWorkers();
            this.setAllWorkers();
            this.layout.doLayout();
        }, this);
    },
    setAllWorkers: function(){
        var items = [];
        var checkboxstate = false;
        this.candidates.each(function(data, index){
            items[index] = {
                boxLabel: '<span class="' + data.data.type + '">' + data.data.name + '</span><br />' + data.data.description + ' (' + data.data.time + ' update)',
                name: 'enclosure[]',
                inputValue: data.data.value,
                autoHeight: true,
                autoWidth: true,
                xtype: 'checkbox'
            };
        });
        items.unshift({
            xtype: 'textfield',
            id: 'find-workers',
            fieldLabel: 'Search',
            msgTarget: 'side',
            autoHeight: true,
            autoWidth: true,
            autoCreate: {
                tag: 'input',
                type: 'text',
                size: 15
            }
        });
        this.allworkers = new Ext.form.FormPanel({
            labelWidth: 75,
            layout: 'fit',
            title: 'All workers',
            header: false,
            bodyStyle: 'padding:15px',
            labelPad: 10,
            defaultType: 'fieldset',
            defaults: {
                msgTarget: 'side'
            },
            layoutConfig: {
                labelSeparator: ''
            },
            items: [{
                title: 'All Workers',
                width: 550,
                cls: 'candities',
                defaultType: 'checkbox',
                autoHeight: true,
                autoWidth: true,
                defaults: {
                    width: 400
                },
                items: items
            }],
            buttons: [{
                text: 'Install',
                handler: function(){
                    Ext.MessageBox.confirm('Confirm', 'Are you sure you want to install?', function(btn){
                        if (btn == 'yes') {
                            this.allworkers.getForm().submit({
                                url: 'updator.php',
                                scope: this,
                                params: {
                                    'action': 'install'
                                },
                                failure: function(form, action){
                                    Ext.MessageBox.alert('Status', 'Install failed.');
                                },//+error message
                                success: function(form, action){
                                    this.modules.reload();
                                    Ext.MessageBox.alert('Status', 'Workers installed successfully.');
                                }
                            });
                        }
                    }, this);
                },
                scope: this
            }, {
                text: 'Check all',
                handler: function(){
                    var checkboxes = this.allworkers.findByType('checkbox');
                    checkboxstate = (!checkboxstate);
                    for (i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].setValue(checkboxstate);
                    }
                },
                scope: this
            }]
        });
        this.centerTabs[2].add(this.allworkers);
        this.centerTabs[2].doLayout();
        Ext.get('find-workers').on('keyup', function(e, el){
            //			this.clearAllWorkers();
            //			this.setAllWorkers();//debugger;
            //			Ext.get('find-workers').set({value:el.value});
            //			Ext.get('find-workers').focus();
            this.searchWorkers(e, el, this.allworkers, 'inputValue')
        }, this, {
            buffer: 150
        });
    },
    clearAllWorkers: function(){
        if (this.allworkers) {
            this.centerTabs[2].remove(this.allworkers);
        }
    }
};
//処理の開始
Ext.onReady(Manage.init, Manage, true);
