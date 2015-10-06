
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

var App = React.createClass({
    
    render: function()
    {
        return (
            <div className="app">
                <PackageList />
            </div>
        );
    }
    
});

var PackageList = React.createClass({

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
            <div className="packages">
                {this.state.packages.map(function(p){
                    return <Package id={p.id} name={p.name} />
                })}
            </div>
        );
    }
});

var Package = React.createClass({
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
            <div className="package">
                {this.state.activities.map(function(activity){
                    return <Activity selected={0} limit={activity.limit} count={activity.count} name={activity.name} />
                })}
            </div>
        );
    }
});


var Activity = React.createClass({
    
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
                slotText = <span>Paikkoja jäljellä enää<b>{slotsLeft}</b>!</span>;
            else
                slotText = <span>Vielä <b>{slotsLeft}</b> paikkaa jäljellä!</span>;
        }

        return (
            <div className="activity">
                <div className="wrapper">
                    <div className="toggle">
                        <div className="button">{icon}</div>
                    </div>
                    <div className="info">
                        <div className="name">{this.props.name}</div>
                        <div className="slots">{slotText}</div>
                    </div>
                </div>
            </div>
        );
    }

});


React.render(<App />, $("#app")[0]);