import React from 'react';
import styled from 'styled-components';
import ContentGrid from '../components/grid/ContentGrid';

const GridContainer = styled('div')`
  .table-active {
    padding: 0 !important;
    border-top: 0;
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
