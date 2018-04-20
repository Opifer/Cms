import { createSelector } from 'reselect';

export const itemEntitiesSelector = state => state.entities.medias;
export const directoryEntitiesSelector = state => state.entities.directories;
export const filtersSelector = state => state.media.filters;
export const selectedSelector = state => state.media.selected;
export const itemsSelector = state => state.media.items;
export const directoriesSelector = state => state.media.directories;
export const directorySelector = state => state.media.directory;

export const activeItemsSelector = createSelector(
  itemsSelector,
  itemEntitiesSelector,
  directorySelector,
  filtersSelector,
  (ids, items, dir, filters) => {
    if (!ids || !items) {
      return [];
    }

    const medias = ids
      .map(id => items[id])
      .filter(i => i);

    if (dir || !filters.search) {
      return medias.filter(item => item.directory_id === dir || (!item.directory_id && !dir));
    }

    return medias;
  }
);

export const activeDirectoriesSelector = createSelector(
  directoryEntitiesSelector,
  directorySelector,
  filtersSelector,
  (directories, dir, filters) => {
    // Hide directories while searching the global state
    if (!dir && filters.search) {
      return [];
    }

    return directories ? Object.keys(directories).filter(i => directories[i].parent_id === dir || (!directories[i].parent_id && !dir)).map(i => directories[i]) : [];
  }
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
