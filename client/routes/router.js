const getEnv = () => (process.env.NODE_ENV !== 'production' ? '/app_dev.php' : '');
export const route = path => `${getEnv()}${path}`;

export const getContentEditPath = id => route(`/admin/content/edit/${id}`);
export const getContentDesignerPath = id => route(`/admin/designer/content/${id}`);
