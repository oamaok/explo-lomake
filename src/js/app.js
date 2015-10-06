
var SetIntervalMixin = {
    componentWillMount: function() {
        this.intervals = [];
    },
    setInterval: function() {
        this.intervals.push(setInterval.apply(null, arguments));
    },
    componentWillUnmount: function() {
        this.intervals.map(clearInterval);
    }
};

var App = React.createClass({displayName: "App",
    
    render: function()
    {
        return (
            React.createElement("div", {className: "app"}, 
                React.createElement(PackageList, null)
            )
        );
    }
    
});

var PackageList = React.createClass({displayName: "PackageList",

    getInitialState: function()
    {
        return {
            packages: []
        };
    },

    componentWillMount: function()
    {
        var that = this;
        $.get("packages.php", function(packages){
            that.setState({
                packages: packages
            });
        });
    },

    render: function()
    {
        return (
            React.createElement("div", {className: "packages"}, 
                this.state.packages.map(function(p){
                    return React.createElement(Package, {id: p.id, name: p.name})
                })
            )
        );
    }
});

var Package = React.createClass({displayName: "Package",
    mixins: [SetIntervalMixin],

    getInitialState: function()
    {
        return {
            activities: []
        };
    },

    componentWillMount: function()
    {
        this.update();

        this.setInterval(this.update, 5000);
    },

    update: function()
    {
        var that = this;
        $.get("activities.php?package=" + this.props.id, function(activities){
            that.setState({
                activities: activities
            });
        });
    },

    render: function()
    {

        return (
            React.createElement("div", {className: "package"}, 
                this.state.activities.map(function(activity){
                    return React.createElement(Activity, {selected: 0, limit: activity.limit, count: activity.count, name: activity.name})
                })
            )
        );
    }
});


var Activity = React.createClass({displayName: "Activity",
    
    render: function()
    {
        var icon = this.props.selected ? String.fromCharCode(0xE837) : String.fromCharCode(0xE836);
        var slotText = "";

        var slotsLeft = this.props.limit - this.props.count;

        if(this.props.limit == 0)
        {
            slotText = "Liukuva paikkamäärä, sisään vaan!";
        }
        else
        {
            if(slotsLeft < 5)
                slotText = React.createElement("span", null, "Paikkoja jäljellä enää", React.createElement("b", null, slotsLeft), "!");
            else
                slotText = React.createElement("span", null, "Vielä ", React.createElement("b", null, slotsLeft), " paikkaa jäljellä!");
        }

        return (
            React.createElement("div", {className: "activity"}, 
                React.createElement("div", {className: "wrapper"}, 
                    React.createElement("div", {className: "toggle"}, 
                        React.createElement("div", {className: "button"}, icon)
                    ), 
                    React.createElement("div", {className: "info"}, 
                        React.createElement("div", {className: "name"}, this.props.name), 
                        React.createElement("div", {className: "slots"}, slotText)
                    )
                )
            )
        );
    }

});


React.render(React.createElement(App, null), $("#app")[0]);