// A single expression
var Expression = React.createClass({
    initializeJquery: function() {
        var expression = this.props.expression;
        var prototype = this.props.prototypes.filter(function(item) {
            return expression.key == item.key;
        })[0];

        var self = this;

        if (prototype.type == 'date') {
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
                <option key={i} value={prototype.key}>{prototype.name}</option>
            )
        }, this);

        var prototype = this.props.prototypes.filter(function(item) {
            return expression.key == item.key;
        })[0];

        if (prototype.constraints) {
            var constraints = prototype.constraints.map(function(constraint, i) {
                return (
                    <option key={i} value={constraint.value}>{constraint.name}</option>
                )
            });
        }

        switch (prototype.type) {
            case 'set':
                var value = '';
                break;
            case 'date':
                var value = <input type="text" className="form-control expression-date-picker" onChange={this.props.changeValue} value={this.props.expression.value}/>;
                break;
            case 'number':
                var value = <input type="number" className="form-control" onChange={this.props.changeValue} value={this.props.expression.value}/>;
                break;
            case 'select':
                var choices = prototype.choices.map(function(choice, i) {
                    return (
                        <option key={i} value={choice.value}>{choice.name}</option>
                    )
                });
                var value = <select className="form-control" onChange={this.props.changeValue} value={this.props.expression.value}>{choices}</select>;
                break;
            default:
                var value = <input type="text" className="form-control" onChange={this.props.changeValue} value={this.props.expression.value}/>;
        }

        if (prototype.type == 'set') {
            var nested = <div className="form-group row"><div className="col-xs-12"><div className="p-l-lg"><ExpressionBuilder changeChildren={this.props.changeChildren} prototypes={this.props.prototypes} expressions={expression.children} root={false} /></div></div></div>;
            var constraintSelect = '';
        } else {
            var nested = '';
            var constraintSelect = <select className="form-control" onChange={this.props.changeConstraint} value={this.props.expression.constraint}>{constraints}</select>
        }

        return (
            <div className="expression-constraint">
                <div className="form-group row">
                    <div className="col-xs-3">
                        <select className="form-control" onChange={this.props.changePrototype} value={this.props.expression.key}>
                            {prototypes}
                        </select>
                    </div>
                    <div className={constraintSelect ? 'col-xs-3' : 'hide'}>
                        {constraintSelect}
                    </div>
                    <div className={value ? 'col-xs-4' : 'hide'}>
                        {value}
                    </div>
                    <div className="col-xs-2">
                        <a className="btn btn-link btn-sm" onClick={this.props.remove}>
                            <i className="material-icons md-18">close</i>
                        </a>
                    </div>
                </div>
                {nested}
            </div>
        );
    }
});

var ExpressionBuilder = React.createClass({
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
            type: prototype.type,
            constraint: (prototype.constraints) ? prototype.constraints[0].value : '',
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
        this.state.expressions[index].type = prototype.type;
        this.state.expressions[index].constraint = prototype.constraints[0].value;
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
                <Expression
                    key={expression.id}
                    expression={expression}
                    prototypes={this.state.prototypes}
                    changePrototype={this.changeExpression.bind(this, expression)}
                    changeConstraint={this.changeConstraint.bind(this, expression)}
                    changeValue={this.changeValue.bind(this, expression)}
                    changeChildren={this.changeChildren.bind(this, expression)}
                    remove={this.removeExpression.bind(this, expression)}>
                </Expression>
            )
        }, this);

        var input;
        if (this.props.root == true) {
            input = <input type="hidden" name={this.props.input} value={JSON.stringify(this.state.expressions)} />;
        }

        var debug;
        if (this.props.root == true && this.props.debug == true) {
            debug = <div><pre>{JSON.stringify(this.state.expressions, null, 2) }</pre></div>;
        }

        return (
            <div className="expression-builder">
                {input}
                {expressions}
                <a className="btn btn-default" onClick={this.addExpression}>
                    <i className="material-icons">add</i> Add expression
                </a>
                {debug}
            </div>
        )
    }
});

function initializeExpressionEngine() {
    var container = document.getElementById('expressionbuilder');
    if (container) {
        ReactDOM.render(
            <ExpressionBuilder
                prototypes={JSON.parse(container.getAttribute('prototypes'))}
                expressions={JSON.parse(container.getAttribute('expressions'))}
                input={container.getAttribute('input')}
                root={true}
                debug={container.getAttribute('debug')}
            />,
            container
        );
    }
}

initializeExpressionEngine();
