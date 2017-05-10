// A single expression
var Expression = React.createClass({displayName: "Expression",
    initializeJquery: function() {
        var expression = this.props.expression;
        var prototype = this.props.prototypes.filter(function(item) {
            return expression.key == item.key;
        })[0];

        var self = this;

        if (prototype && prototype.type == 'date') {
            $(ReactDOM.findDOMNode(this)).find('input.expression-date-picker').datetimepicker({
                format: 'YYYY-MM-DD',
            }).on('dp.change', function(e) {
                self.changeDate(e.date.format('YYYY-MM-DD'));
            });
        }
    },
    changeDate: function (date) {
        this.props.expression.value = date;

        this.props.changeValue({target:{value:date}});
    },
    componentDidMount: function() {
        this.initializeJquery();
    },
    componentDidUpdate: function() {
        this.initializeJquery();
    },
    render: function() {
        var expression = this.props.expression;

        var prototypes = this.props.prototypes.map(function(prototype, i) {
            return (
                React.createElement("option", {key: i, value: prototype.key}, prototype.name)
            )
        }, this);

        var prototype = this.props.prototypes.filter(function(item) {
            return expression.key == item.key;
        })[0];

        if (!prototype) {
            return React.createElement("div", null);
        }

        if (prototype.constraints && prototype.constraints.length > 0) {
            var constraints = prototype.constraints.map(function(constraint, i) {
                return (
                    React.createElement("option", {key: i, value: constraint.value}, constraint.name)
                )
            });
        }

        var nested, value, constraintSelect = null;

        switch (prototype.type) {
            case 'date':
                value = React.createElement("input", {type: "text", className: "form-control expression-date-picker", onChange: this.props.changeValue, value: this.props.expression.value});
                break;
            case 'number':
                value = React.createElement("input", {type: "number", className: "form-control", onChange: this.props.changeValue, value: this.props.expression.value});
                break;
            case 'select':
                var choices = prototype.choices.map(function(choice, i) {
                    return (
                        React.createElement("option", {key: i, value: choice.value}, choice.name)
                    )
                });
                value = React.createElement("select", {className: "form-control", onChange: this.props.changeValue, value: this.props.expression.value}, choices);
                break;
            case 'text':
                value = React.createElement("input", {type: "text", className: "form-control", onChange: this.props.changeValue, value: this.props.expression.value});
                break;
            default:
                value = null;
                break;
        }

        if (prototype.type == 'set') {
            nested = React.createElement("div", {className: "form-group row"}, React.createElement("div", {className: "col-xs-12"}, React.createElement("div", {className: "p-l-lg"}, React.createElement(ExpressionBuilder, {changeChildren: this.props.changeChildren, prototypes: this.props.prototypes, expressions: expression.children, root: false}))));
            constraintSelect = '';
        } else if (typeof(constraints) !== 'undefined' && constraints.length) {
            constraintSelect = React.createElement("select", {className: "form-control", onChange: this.props.changeConstraint, value: this.props.expression.constraint}, constraints)
        }

        return (
            React.createElement("div", {className: "expression-constraint"}, 
                React.createElement("div", {className: "form-group row"}, 
                    React.createElement("div", {className: "col-xs-4 p-r-0"}, 
                        React.createElement("select", {className: "form-control", onChange: this.props.changePrototype, value: this.props.expression.key}, 
                            prototypes
                        )
                    ), 
                    constraintSelect ? React.createElement("div", {className: "col-xs-2 p-r-0"}, constraintSelect) : null, 
                    value ? React.createElement("div", {className: "col-xs-4 p-r-0"}, value) : null, 
                    React.createElement("div", {className: "col-xs-2"}, 
                        React.createElement("a", {className: "btn btn-link btn-sm", onClick: this.props.remove}, 
                            React.createElement("i", {className: "material-icons md-18"}, "close")
                        )
                    )
                ), 
                nested
            )
        );
    }
});

var ExpressionBuilder = React.createClass({displayName: "ExpressionBuilder",
    getInitialState: function() {
        return {
            expressions: this.props.expressions,
            prototypes: this.props.prototypes
        };
    },
    updateExpressions: function(expressions) {
        this.setState({expressions: expressions});
        if (typeof(this.props.changeChildren) != 'undefined') {
            this.props.changeChildren(expressions);
        }
    },
    addExpression: function(event) {
        var prototype = this.state.prototypes[0];

        var expression = {
            id: Date.now(),
            key: prototype.key,
            selector: prototype.selector,
            constraint: (typeof(prototype.constraints) !== 'undefined' && prototype.constraints.length) ? prototype.constraints[0].value : '',
            type: prototype.type,
            value: (prototype.choices && prototype.choices.length > 0) ? prototype.choices[0].value : '',
            children: []
        };

        var expressions = this.state.expressions.concat([expression]);
        this.updateExpressions(expressions);
    },
    removeExpression: function(expression) {
        var expressions = this.state.expressions.filter(function(item){
            return expression.id !== item.id;
        });

        this.updateExpressions(expressions);
    },
    changeExpression: function(expression, event) {
        var key = event.target.value;
        var index = this.state.expressions.findIndex(function(item) {
            return expression.id == item.id;
        });

        var prototype = this.state.prototypes.filter(function(item) {
            return key == item.key;
        })[0];

        this.state.expressions[index].key = prototype.key;
        this.state.expressions[index].selector = prototype.selector;
        this.state.expressions[index].constraint = (prototype.constraints && prototype.constraints.length > 0) ? prototype.constraints[0].value : '';
        this.state.expressions[index].type = prototype.type;
        this.state.expressions[index].value = (prototype.choices && prototype.choices.length > 0) ? prototype.choices[0].value : '';

        this.updateExpressions(this.state.expressions);
    },
    changeConstraint: function (expression, event) {
        var constraint = event.target.value;
        var index = this.state.expressions.findIndex(function(item) {
            return expression.id == item.id;
        });

        this.state.expressions[index].constraint = constraint;
        this.updateExpressions(this.state.expressions);
    },
    changeValue: function (expression, event) {
        var value = event.target.value;
        var index = this.state.expressions.findIndex(function(item) {
            return expression.id == item.id;
        });

        this.state.expressions[index].value = value;
        this.updateExpressions(this.state.expressions);
    },
    changeChildren: function (expression, children) {
        var index = this.state.expressions.findIndex(function(item) {
            return expression.id == item.id;
        });

        this.state.expressions[index].children = children;
        this.updateExpressions(this.state.expressions);
    },
    render: function() {
        var expressions = this.state.expressions.map(function(expression) {
            return (
                React.createElement(Expression, {
                    key: expression.id, 
                    expression: expression, 
                    prototypes: this.state.prototypes, 
                    changePrototype: this.changeExpression.bind(this, expression), 
                    changeConstraint: this.changeConstraint.bind(this, expression), 
                    changeValue: this.changeValue.bind(this, expression), 
                    changeChildren: this.changeChildren.bind(this, expression), 
                    remove: this.removeExpression.bind(this, expression)}
                )
            )
        }, this);

        var input;
        if (this.props.root == true) {
            input = React.createElement("input", {type: "hidden", name: this.props.input, value: JSON.stringify(this.state.expressions)});
        }

        var debug;
        if (this.props.root == true && this.props.debug == true) {
            debug = React.createElement("div", null, React.createElement("pre", null, JSON.stringify(this.state.expressions, null, 2) ));
        }

        return (
            React.createElement("div", {className: "expression-builder"}, 
                input, 
                expressions, 
                React.createElement("a", {className: "btn btn-default", onClick: this.addExpression}, 
                    React.createElement("i", {className: "material-icons"}, "add"), " Add expression"
                ), 
                debug
            )
        )
    }
});

function initializeExpressionEngine() {
    var container = document.getElementById('expressionbuilder');
    if (container) {
        ReactDOM.render(
            React.createElement(ExpressionBuilder, {
                prototypes: JSON.parse(container.getAttribute('prototypes')), 
                expressions: JSON.parse(container.getAttribute('expressions')), 
                input: container.getAttribute('input'), 
                root: true, 
                debug: container.getAttribute('debug')}
            ),
            container
        );
    }
}

initializeExpressionEngine();

//# sourceMappingURL=react.js.map
