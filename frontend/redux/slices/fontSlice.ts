import { createSlice } from "@reduxjs/toolkit";


export interface FontState {
    fontList: any;
    fontGroupList: any;
}

const initialState: FontState = {
    fontList: [],
    fontGroupList: [],
};

export const fontSlice = createSlice({
    name: 'fontSlice',
    initialState,
    reducers: {
        setFontList: (state, action) => {
            state.fontList = action.payload;
        },
        setFontGroupList: (state, action) => {
            state.fontGroupList = action.payload;
        },
    },
});

export const { setFontList, setFontGroupList } = fontSlice.actions;
export default fontSlice.reducer;