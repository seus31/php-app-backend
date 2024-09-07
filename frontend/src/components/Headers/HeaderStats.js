import React from "react";
import CardStats from "../Cards/CardStats";

export default function HeaderStats() {
  return (
    <>
      {/* Header */}
      {/* <div className="relative bg-lightBlue-600 md:pt-32 pb-32 pt-12"> */}
      <div className="relative bg-lightBlue-600 pb-32 pt-12">
        <div className="px-4 md:px-10 mx-auto w-full">
          <div>
            <div className="flex flex-wrap">
              {/*
              <div className="w-full lg:w-6/12 xl:w-3/12 px-4">
                <CardStats
                  statSubtitle="etst"
                  statTitle="350,897"
                  statArrow="up"
                  statPercent="3.48"
                  statPercentColor="text-emerald-500"
                  statDescripiron="test"
                  statIconName="far fa-chart-bar"
                  statIconColor="bg-red-500"
                />
              </div>*/}
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
