
import { getFonts } from "@/lib/actions";
import FontAdd from "@/components/fonts/FontAdd";
import FontList from "@/components/fonts/FontList";

interface Props {
  searchParams: {
    [key: string]: string | undefined
  }
}

const HomePage = async ({ searchParams }: Props) => {

  const fonts = await getFonts({
    page: Number(searchParams?.page) || 1
  });

  return (
    <div className="w-full mt-2 mb-2">
      <div className="w-full flex">
        <div className="w-full md:w-6/12">
          <h1 className="text-3xl text-black font-normal">Our Fonts</h1>
          <p className="text-base text-gray-500 font-normal">
            Browse a list of Zepto fonts build your font group.
          </p>
        </div>
        <div className="w-full md:w-6/12">
          <FontAdd />
        </div>

      </div>
      <div className="w-full">
        <FontList className="w-full mt-4 mb-2" fonts={fonts} />
      </div>
    </div>
  );
}

export default HomePage;