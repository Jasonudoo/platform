Ext.ns('oseMscAddon');
	oseMscAddon.msg = new Ext.App();
	
	var addonContentEventbooking_sm = new Ext.grid.CheckboxSelectionModel({
		//singleSelect:true,
		listeners:{
			selectionchange: function(sm,node){
				oseMscAddon.eventbooking.grid.getTopToolbar().findById('show-to-members').setDisabled(sm.getCount() < 1); // >
				oseMscAddon.eventbooking.grid.getTopToolbar().findById('show-to-all').setDisabled(sm.getCount() < 1); // >
				oseMscAddon.eventbooking.grid.getTopToolbar().findById('hide-to-members').setDisabled(sm.getCount() < 1); // >
			}
		}
	});
	
	var addonContentEventbooking_store = new Ext.data.Store({
		  proxy: new Ext.data.HttpProxy({
	            url: 'index.php?option=com_osemsc&controller=content',
	            method: 'POST'
	      }),
		  baseParams:{task: "action",action:'content.eventbooking.getList',msc_id:''}, 
		  reader: new Ext.data.JsonReader({   
		            
		    root: 'results',
		    totalProperty: 'total',
		    idProperty: 'id'
		  },[ 
		    {name: 'id', type: 'int', mapping: 'id'},
		    {name: 'treename', type: 'string', mapping: 'treename'},
		    {name: 'controlled', type: 'string', mapping: 'controlled'},
		    {name: 'type', type: 'string', mapping: 'type'}
		  ]),
		  //sortInfo:{field: 'user_id', direction: "ASC"},
		  listeners: {
			 
		  	beforeload: function(store,records,options)	{
			  	var levellimit = oseMscAddon.eventbooking.grid.getTopToolbar().findById('combo').getValue();
		  		store.setBaseParam('msc_id',oseMscs.msc_id);
		  		store.setBaseParam('levellimit',levellimit);
			  	
		  	}
		  }
	});
	
	
	var addonContentEventbooking_cm = new Ext.grid.ColumnModel({
		defaults:{},
		columns:[ 
			addonContentEventbooking_sm,
			new Ext.grid.RowNumberer({header:'#'}),
		    {id:'id',header: Joomla.JText._("ID"), width: 200, dataIndex: 'id',hidden:true},
	        {header: Joomla.JText._('Category'),  dataIndex: 'treename'},
	        {header: Joomla.JText._("Controlled"),  dataIndex: 'controlled'},
	        {header: Joomla.JText._("Type"),  dataIndex: 'type'}
	  	]
  	});
	
	oseMscAddon.eventbooking = new Ext.Panel({
		//title: 'Menu',
		listeners:{
			render: function(p)	{
				addonContentEventbooking_store.load();
			}
		},
		
		items:[{
			ref:'grid',
			xtype:'grid',
			autoScroll:true,
			height: 400,
			viewConfig:{forceFit: true},
			
			store: addonContentEventbooking_store,
			sm: addonContentEventbooking_sm,
			cm: addonContentEventbooking_cm,
			
			bbar: new Ext.PagingToolbar({
	    		pageSize: 20,
	    		store: addonContentEventbooking_store,
	    		displayInfo: true,
	    		displayMsg: Joomla.JText._('Displaying_topics')+' {0} - {1} '+Joomla.JText._('of')+' {2}',
				emptyMsg: Joomla.JText._("No_topics_to_display")
		    }),
		    
		    tbar:[{
		    	text: Joomla.JText._('Show_to_Members'),
		    	id:'show-to-members',
		    	disabled:true,
		    	handler: function()	{
		    		var ids = oseMscAddon.eventbooking.grid.getSelectionModel().getSelections();
		    		var catids = new Array();
		    		for(i=0;i < ids.length; i++)	{
		    			var r = ids[i];
		    			catids[i] = r.id;
		    		}
		    		
		    		Ext.Ajax.request({
		    			url:'index.php?option=com_osemsc&controller=content',
		    			params:{
	    					task:'action',action:'content.eventbooking.changeStatus','catids[]':catids,
		    				msc_id:oseMsc.msc_id, status: '1'
		    			},
		    			success: function(response,opt)	{
		    				var msg = Ext.decode(response.responseText);
		    				oseMscAddon.msg.setAlert(msg.title,msg.content);
		    				
		    				if(msg.success)	{
		    					oseMscAddon.eventbooking.grid.getSelectionModel().clearSelections();
		    					oseMscAddon.eventbooking.grid.getStore().reload();
		    					oseMscAddon.eventbooking.grid.getView().refresh();
		    				}
		    			}
		    		});
		    	}
		    },{
		    	text: Joomla.JText._('Show_to_All'),
		    	id:'show-to-all',
		    	disabled:true,
		    	handler: function()	{
		    		var ids = oseMscAddon.eventbooking.grid.getSelectionModel().getSelections();
		    		
		    		var catids = new Array();
		    		for(i=0;i < ids.length; i++)	{
		    			var r = ids[i];
		    			catids[i] = r.id;
		    		}
		    		
		    		Ext.Ajax.request({
		    			url:'index.php?option=com_osemsc&controller=content',
		    			params:{
		    				task:'action',action:'content.eventbooking.changeStatus','catids[]':catids,
		    				msc_id:oseMsc.msc_id, status: '0'
		    			},
		    			success: function(response,opt)	{
		    				var msg = Ext.decode(response.responseText);
		    				oseMscAddon.msg.setAlert(msg.title,msg.content);
		    				
		    				if(msg.success)	{
		    					oseMscAddon.eventbooking.grid.getSelectionModel().clearSelections();
		    					oseMscAddon.eventbooking.grid.getStore().reload();
		    					oseMscAddon.eventbooking.grid.getView().refresh();
		    				}
		    			}
		    		});
		    	}
		    },{
		    	text: Joomla.JText._('Hide_to_Members'),
		    	id:'hide-to-members',
		    	hidden: true,
		    	disabled:true,
		    	handler: function()	{
		    		var ids = oseMscAddon.eventbooking.grid.getSelectionModel().getSelections();
		    		
		    		var catids = new Array();
		    		for(i=0;i < ids.length; i++)	{
		    			var r = ids[i];
		    			catids[i] = r.id;
		    		}
		    		
		    		Ext.Ajax.request({
		    			url:'index.php?option=com_osemsc&controller=content',
		    			params:{
		    				task:'action',action:'content.eventbooking.changeStatus','catids[]':catids,
		    				msc_id:oseMsc.msc_id,status: '-1'
		    			},
		    			success: function(response,opt)	{
		    				var msg = Ext.decode(response.responseText);
		    				oseMscAddon.msg.setAlert(msg.title,msg.content);
		    				
		    				if(msg.success)	{
		    					oseMscAddon.eventbooking.grid.getSelectionModel().clearSelections();
		    					oseMscAddon.eventbooking.grid.getStore().reload();
		    					oseMscAddon.eventbooking.grid.getView().refresh();
		    				}
		    			}
		    		});
		    	}
		    },'->',{
		    	text: Joomla.JText._('Max_Levels')
		    },{
	        	xtype: 'combo',
	        	width: 200,
	        	id:'combo',
	        	hiddenName: 'levellimit',
	        	typeAhead: true,
			    triggerAction: 'all',
			    lazyRender:true,
			    mode: 'local',
			    store: new Ext.data.ArrayStore({
			        id: 0,
			        fields: [
			            'levellimit'
			        ],
			        data: [
			        	['1'],
			        	['2'],
	                    ['3'],
			        	['4'],
			        	['5'],
			        	['6'],
			        	['7'],
			        	['8'],
			        	['9'],
			        	['10'],
			        	['11'],
			        	['12'],
			        	['13'],
			        	['14'],
			        	['15'],
			        	['16'],
			        	['17'],
			        	['18'],
			        	['19'],
			        	['20']

			        ]
			    }),
			    valueField: 'levellimit',
			    displayField: 'levellimit',
			    
			    listeners: {
			        // delete the previous query in the beforequery event or set
			        // combo.lastQuery = null (this will reload the store the next time it expands)
			        beforequery: function(qe){
			            delete qe.combo.lastQuery;
			        },
	    			afterrender: function(e)	{
	    				e.setValue('10');
	    			},
	    			select: function(c,r,i)	{
	    				
		    			oseMscAddon.eventbooking.grid.getStore().reload({
		    				params:{levellimit:r.data.levellimit,msc_id:oseMscs.msc_id}
		    			});
		    		}
		        }
		    },'-',
				new Ext.ux.form.SearchField({
	                store: addonContentEventbooking_store,
	                paramName: 'search',
	                width:150
	            })
			]
		}]
	});