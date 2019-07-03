import styled from 'styled-components';
import Cell from './Cell';

const IndentedCell = styled(Cell)`
  padding-left: ${props => props.level * 20}px !important;
`;

export default IndentedCell;
