import { configureStore } from "@reduxjs/toolkit";
import { setupListeners } from '@reduxjs/toolkit/query'
import fontSlice from "./slices/fontSlice";

export const store = configureStore({
    reducer: {
        font: fontSlice
    },
});

setupListeners(store.dispatch)