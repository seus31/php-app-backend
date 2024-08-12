import React, { createContext, useContext } from 'react';

// ApiContextの作成
const ApiContext = createContext(undefined);

// ApiProviderコンポーネントの定義
export const ApiProvider = ({ apiUrl, children }) => {
  return (
    <ApiContext.Provider value={apiUrl}>
      {children}
    </ApiContext.Provider>
  );
};

// useApiフックの定義
export const useApi = () => {
  const context = useContext(ApiContext);
  if (context === undefined) {
    throw new Error('useApi must be used within an ApiProvider');
  }
  return context;
};
