import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import styled from 'styled-components/macro';
import { TABLE_HEADING_TYPE, TABLE_NODATA_TYPE, TABLE_DETAIL_TYPE } from '@devexpress/dx-grid-core';
import { Getter, Template, Plugin } from '@devexpress/dx-react-core';
import { Table } from '@devexpress/dx-react-grid-bootstrap4';
import SmallIcon from '../../icons/SmallIcon';

const pluginDependencies = [
  { name: 'Table' },
];

const ACTIONS_COLUMN_TYPE = 'actionsColumnType';

const ButtonGroup = styled('div')`
  i {
    margin-right: 5px;
  }
`;

const tableColumnsWithActions = (tableColumns, width) => [
  ...tableColumns,
  { key: ACTIONS_COLUMN_TYPE, type: ACTIONS_COLUMN_TYPE, width }
];

const isHeadingActionsTableCell = (tableRow, tableColumn) => tableRow.type === TABLE_HEADING_TYPE
  && tableColumn.type === ACTIONS_COLUMN_TYPE;

const isActionsTableCell = (tableRow, tableColumn) => tableRow.type !== TABLE_HEADING_TYPE
  && tableRow.type !== TABLE_NODATA_TYPE
  && tableRow.type !== TABLE_DETAIL_TYPE
  && tableColumn.type === ACTIONS_COLUMN_TYPE;

class ActionsColumn extends PureComponent {
  render = () => {
    const {
      actions,
      width,
    } = this.props;

    const tableColumnsComputed = ({ tableColumns }) => tableColumnsWithActions(tableColumns, width);

    return (
      <Plugin
        name="ActionsColumn"
        dependencies={pluginDependencies}
      >
        <Getter name="tableColumns" computed={tableColumnsComputed} />

        <Template
          name="tableCell"
          predicate={({ tableRow, tableColumn }) => isHeadingActionsTableCell(tableRow, tableColumn)}
        >
          <Table.Cell>Actions</Table.Cell>
        </Template>
        <Template
          name="tableCell"
          predicate={({ tableRow, tableColumn }) => isActionsTableCell(tableRow, tableColumn)}
        >
          {params => (
            <Table.Cell {...params} row={params.tableRow.row}>
              <ButtonGroup>
                {actions.map(action => (
                  <SmallIcon
                    key={action.icon}
                    icon={action.icon}
                    onClick={() => action.action(params.tableRow.row)}
                  />
                ))}
              </ButtonGroup>
            </Table.Cell>
          )}
        </Template>
      </Plugin>
    );
  }
}

ActionsColumn.propTypes = {
  actions: PropTypes.arrayOf(PropTypes.PropTypes.shape({
    icon: PropTypes.node,
    action: PropTypes.func.isRequired
  })).isRequired,
  width: PropTypes.number,
};

ActionsColumn.defaultProps = {
  width: 240,
};

export default ActionsColumn;
