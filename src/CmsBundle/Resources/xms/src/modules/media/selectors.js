import { createSelector } from 'reselect';

export const itemEntitiesSelector = state => state.entities.medias;
export const directoryEntitiesSelector = state => state.entities.directories;

// export const itemsSelector = state => state.media.items;
// export const directoriesSelector = state => state.media.directories;
export const directorySelector = state => state.media.directory;

export const activeItemsSelector = createSelector(
  itemEntitiesSelector,
  directorySelector,
  (items, dir) => items ? Object.keys(items).filter(i => items[i].directory_id === dir || (!items[i].directory_id && !dir)).map(i => items[i]) : []
);

export const activeDirectoriesSelector = createSelector(
  directoryEntitiesSelector,
  directorySelector,
  (directories, dir) => directories ? Object.keys(directories).filter(i => directories[i].parent_id === dir || (!directories[i].parent_id && !dir)).map(i => directories[i]) : []
);

export const currentDirectorySelector = createSelector(
  directoryEntitiesSelector,
  directorySelector,
  (directories, dir) => (dir) ? directories[dir] : null
);

export const parentDirectorySelector = createSelector(
  directoryEntitiesSelector,
  currentDirectorySelector,
  (directories, directory) => (directory && directory.parent_id) ? directories[directory.parent_id] : null
);
