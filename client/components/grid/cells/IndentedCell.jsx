import styled from 'styled-components/macro';
import Cell from './Cell';

const IndentedCell = styled(Cell)`
  cursor: pointer;
  padding-left: ${props => props.level * 20}px !important;
`;

export default IndentedCell;
