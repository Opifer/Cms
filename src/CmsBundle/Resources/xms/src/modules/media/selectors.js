import { createSelector } from 'reselect';

export const itemEntitiesSelector = state => state.entities.medias;
export const directoryEntitiesSelector = state => state.entities.directories;

export const itemsSelector = state => state.media.items;
export const directoriesSelector = state => state.media.directories;
export const directorySelector = state => state.media.directory;

export const activeItemsSelector = createSelector(
  itemsSelector,
  itemEntitiesSelector,
  directorySelector,
  (ids, items, dir) => {
    if (!ids || !items) {
      return [];
    }

    return ids
      .map(id => items[id])
      .filter(item => item.directory_id === dir || (!item.directory_id && !dir));
  }
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
