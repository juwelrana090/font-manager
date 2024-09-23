'use client'

import React, { useEffect, useState } from 'react';
import { Space, Table } from 'antd';
import type { TableProps } from 'antd';

const { Column } = Table;

interface DataType {
  key: React.Key;
  title: string;
  slug: string;
  font_url: string;
}

interface FontProps {
  className?: string;
  fonts?: any;
}

const FontList = ({ className, fonts }: FontProps) => {
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<DataType[]>([]);
  const [currentPage, setCurrentPage] = useState(1); // Pagination state
  const [pageSize, setPageSize] = useState(5); // Number of items per page

  // Function to inject font-face rules dynamically
  const addFontFace = (fontTitle: string, fontUrl: string) => {
    const fontStyle = document.createElement('style');
    fontStyle.innerHTML = `
      @font-face {
        font-family: '${fontTitle}';
        src: url('${fontUrl}') format('truetype');
        font-weight: normal;
        font-style: normal;
      }
    `;
    document.head.appendChild(fontStyle);
  };

  useEffect(() => {
    setLoading(true);

    let fontList: DataType[] = [];

    if (fonts.data) {
      fonts.data.forEach((font: any) => {
        fontList.push({
          key: font.id,
          title: font.title,
          slug: font.slug,
          font_url: font.font_url,
        });

        // Dynamically inject the font using the font URL
        addFontFace(font.slug, font.font_url);
      });
    }

    setData(fontList);

    const delay = setTimeout(() => {
      setLoading(false);
    }, 1000);
    return () => clearTimeout(delay);
  }, [fonts]);

  const onPaginationChange = (page: number, pageSize?: number) => {
    setCurrentPage(page);
    if (pageSize) setPageSize(pageSize);
  };

  return (
    <div className={`${className}`}>
      <Table<DataType>
        loading={loading}
        dataSource={data}
        pagination={{
          current: currentPage,
          pageSize: pageSize,
          total: data.length,
          showSizeChanger: true,
          onChange: onPaginationChange,
          position: ['bottomCenter'],
        }}
      >
        <Column title="Font Name" dataIndex="title" key="title" />
        <Column
          title="Preview"
          dataIndex="font_url"
          key="font_url"
          render={(_: string, record: DataType) => (
            <span style={{ fontFamily: record.slug }} className="text-base font-normal">
              Example Style
            </span>
          )}
        />
        <Column
          title="Action"
          key="action"
          render={(_: any, record: DataType) => (
            <Space size="middle" className="flex justify-end">
              <span className="text-red-600 hover:text-red-700 text-base font-normal cursor-pointer">
                Delete
              </span>
            </Space>
          )}
        />
      </Table>
    </div>
  );
};

export default FontList;
