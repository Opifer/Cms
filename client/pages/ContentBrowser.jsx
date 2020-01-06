import React from 'react';
import styled from 'styled-components/macro';
import ContentGrid from '../components/grid/ContentGrid';

const GridContainer = styled('div')`
  margin-bottom: 40px;

  .table-active {
    padding: 0 !important;
    border-top: 0;
  }
  .table-active .table>tbody>tr:last-child>td {
    border-bottom: none;
  }
  .w-25 {
    width: 250px;
  }
`;

const ContentBrowser = () => (
  <GridContainer>
    <ContentGrid />
  </GridContainer>
);

export default ContentBrowser;
